<?php

namespace Gecche\Multidomain\Horizon;

use Laravel\Horizon\SupervisorOptions as BaseSupervisorOptions;

/**
 * Class SupervisorOptions
 *
 * @property string $domain
 *
 * @package Gecche\Multidomain\Horizon
 */
class SupervisorOptions extends BaseSupervisorOptions
{
    public string $domain = 'localhost';

    /**
     * Create a new worker options instance.
     *
     * @param  string  $name
     * @param  string  $connection
     * @param  string  $queue
     * @param  string  $workersName
     * @param  string  $balance
     * @param  int  $backoff
     * @param  int  $maxTime
     * @param  int  $maxJobs
     * @param  int  $maxProcesses
     * @param  int  $minProcesses
     * @param  int  $memory
     * @param  int  $timeout
     * @param  int  $sleep
     * @param  int  $maxTries
     * @param  bool  $force
     * @param  int  $nice
     * @param  int  $balanceCooldown
     * @param  int  $balanceMaxShift
     * @param  int  $parentId
     * @param string $domain
     */
    public function __construct($name,
                                $connection,
                                $queue = null,
                                $workersName = 'default',
                                $balance = 'off',
                                $backoff = 0,
                                $maxTime = 0,
                                $maxJobs = 0,
                                $maxProcesses = 1,
                                $minProcesses = 1,
                                $memory = 128,
                                $timeout = 60,
                                $sleep = 3,
                                $maxTries = 0,
                                $force = false,
                                $nice = 0,
                                $balanceCooldown = 3,
                                $balanceMaxShift = 1,
                                $parentId = 0,
                                string $domain = 'localhost')
    {
        parent::__construct($name, $connection, $queue, $workersName, $balance, $backoff, $maxTime, $maxJobs, $maxProcesses, $minProcesses, $memory, $timeout, $sleep, $maxTries, $force, $nice, $balanceCooldown, $balanceMaxShift, $parentId);

        $this->domain = $domain;
    }

    /**
     * Get the command-line representation of the options for a supervisor.
     *
     * @return string
     */
    public function toSupervisorCommand()
    {
        return SupervisorCommandString::fromOptions($this);
    }

    /**
     * Get the command-line representation of the options for a worker.
     *
     * @return string
     */
    public function toWorkerCommand()
    {
        return WorkerCommandString::fromOptions($this);
    }

    /**
     * Convert the options to a raw array.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'balance'         => $this->balance,
            'connection'      => $this->connection,
            'queue'           => $this->queue,
            'backoff'         => $this->backoff,
            'force'           => $this->force,
            'maxProcesses'    => $this->maxProcesses,
            'minProcesses'    => $this->minProcesses,
            'maxTries'        => $this->maxTries,
            'maxTime'         => $this->maxTime,
            'maxJobs'         => $this->maxJobs,
            'memory'          => $this->memory,
            'nice'            => $this->nice,
            'name'            => $this->name,
            'workersName'     => $this->workersName,
            'sleep'           => $this->sleep,
            'timeout'         => $this->timeout,
            'balanceCooldown' => $this->balanceCooldown,
            'balanceMaxShift' => $this->balanceMaxShift,
            'domain'          => $this->domain
        ];
    }
}
