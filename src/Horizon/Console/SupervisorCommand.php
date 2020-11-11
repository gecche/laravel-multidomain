<?php

namespace Gecche\Multidomain\Horizon\Console;

use Gecche\Multidomain\Horizon\SupervisorOptions;
use Laravel\Horizon\Console\SupervisorCommand as BaseSupervisorCommand;

/**
 * Class SupervisorCommand
 *
 * @package Gecche\Multidomain\Horizon\Console
 */
class SupervisorCommand extends BaseSupervisorCommand
{
    /**
     * Get the supervisor options.
     *
     * @return SupervisorOptions
     */
    protected function supervisorOptions()
    {
        $backoff = $this->hasOption('backoff')
                    ? $this->option('backoff')
                    : $this->option('delay');

        return new SupervisorOptions(
            $this->argument('name'),
            $this->argument('connection'),
            $this->getQueue($this->argument('connection')),
            $this->option('workers-name'),
            $this->option('balance'),
            $backoff,
            $this->option('max-time'),
            $this->option('max-jobs'),
            $this->option('max-processes'),
            $this->option('min-processes'),
            $this->option('memory'),
            $this->option('timeout'),
            $this->option('sleep'),
            $this->option('tries'),
            $this->option('force'),
            $this->option('nice'),
            $this->option('balance-cooldown'),
            $this->option('balance-max-shift'),
            $this->option('parent-id'),
            $this->option('domain')
        );
    }
}
