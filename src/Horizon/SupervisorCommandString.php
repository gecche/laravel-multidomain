<?php

namespace Gecche\Multidomain\Horizon;

use Laravel\Horizon\SupervisorCommandString as BaseSupervisorCommandString;
use Laravel\Horizon\SupervisorOptions;

/**
 * Class SupervisorCommandString
 *
 * @package Gecche\Multidomain\Horizon
 */
class SupervisorCommandString extends BaseSupervisorCommandString
{
    /**
     * Get the additional option string for the command.
     *
     * @param  SupervisorOptions  $options
     * @return string
     */
    public static function toOptionsString(SupervisorOptions $options)
    {
        return QueueCommandString::toSupervisorOptionsString($options);
    }
}
