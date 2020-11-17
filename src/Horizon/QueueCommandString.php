<?php

namespace Gecche\Multidomain\Horizon;

use Laravel\Horizon\QueueCommandString as BaseQueueCommandString;
use Laravel\Horizon\SupervisorOptions as BaseSupervisorOptions;

/**
 * Class QueueCommandString
 *
 * @package Gecche\Multidomain\Horizon
 */
class QueueCommandString extends BaseQueueCommandString
{
    /**
     * Get the additional option string for the command.
     *
     * @param  BaseSupervisorOptions  $options
     * @param  bool  $paused
     * @return string
     */
    public static function toOptionsString(BaseSupervisorOptions $options, $paused = false)
    {
        $string = sprintf('--backoff=%s --max-time=%s --max-jobs=%s --memory=%s --queue="%s" --sleep=%s --timeout=%s --tries=%s --domain="%s"',
            $options->backoff, $options->maxTime, $options->maxJobs, $options->memory,
            $options->queue, $options->sleep, $options->timeout, $options->maxTries,
            (($options instanceof SupervisorOptions) ? $options->domain : 'localhost')
        );

        if ($options->force) {
            $string .= ' --force';
        }

        if ($paused) {
            $string .= ' --paused';
        }

        return $string;
    }
}
