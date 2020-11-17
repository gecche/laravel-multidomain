<?php

namespace Gecche\Multidomain\Horizon\MasterSupervisorCommands;

use Laravel\Horizon\MasterSupervisor;
use Gecche\Multidomain\Horizon\SupervisorOptions;
use Gecche\Multidomain\Horizon\SupervisorProcess;
use Laravel\Horizon\MasterSupervisorCommands\AddSupervisor as BaseAddSupervisor;

/**
 * Class AddSupervisor
 *
 * @package Gecche\Multidomain\Horizon\MasterSupervisorCommands
 */
class AddSupervisor extends BaseAddSupervisor
{
    /**
     * Process the command.
     *
     * @param  MasterSupervisor  $master
     * @param  array  $options
     * @return void
     */
    public function process(MasterSupervisor $master, array $options)
    {
        $options = SupervisorOptions::fromArray($options);

        $master->supervisors[] = new SupervisorProcess(
            $options, $this->createProcess($master, $options), function ($type, $line) use ($master) {
            $master->output($type, $line);
        });
    }
}
