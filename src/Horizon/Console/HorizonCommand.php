<?php

namespace Gecche\Multidomain\Horizon\Console;

use Laravel\Horizon\Contracts\MasterSupervisorRepository;
use Laravel\Horizon\MasterSupervisor;
use Gecche\Multidomain\Horizon\ProvisioningPlan;
use Laravel\Horizon\Console\HorizonCommand as BaseHorizonCommand;

/**
 * Class HorizonCommand
 *
 * @package Gecche\Multidomain\Horizon\Console
 */
class HorizonCommand extends BaseHorizonCommand
{
    /**
     * Execute the console command.
     *
     * @param  MasterSupervisorRepository  $masters
     * @return void
     */
    public function handle(MasterSupervisorRepository $masters)
    {
        if ($masters->find(MasterSupervisor::name())) {
            return $this->comment('A master supervisor is already running on this machine.');
        }

        $master = (new MasterSupervisor)->handleOutputUsing(function ($type, $line) {
            $this->output->write($line);
        });

        ProvisioningPlan::get(MasterSupervisor::name(), $this->option('domain'))->deploy(
            $this->option('environment') ?? config('horizon.env') ?? config('app.env')
        );

        $this->info('Horizon started successfully.');

        pcntl_async_signals(true);

        pcntl_signal(SIGINT, function () use ($master) {
            $this->line('Shutting down...');

            return $master->terminate();
        });

        $master->monitor();
    }
}
