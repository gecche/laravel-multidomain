<?php

namespace Gecche\Multidomain\Horizon;

use Gecche\Multidomain\Horizon\MasterSupervisorCommands\AddSupervisor;
use Laravel\Horizon\Contracts\HorizonCommandQueue;
use Laravel\Horizon\MasterSupervisor;
use Laravel\Horizon\ProvisioningPlan as BaseProvisioningPlan;
use Laravel\Horizon\SupervisorOptions as BaseSupervisorOptions;

/**
 * Class ProvisioningPlan
 *
 * @package Gecche\Multidomain\Horizon
 */
class ProvisioningPlan extends BaseProvisioningPlan
{
    /**
     * Get the current provisioning plan.
     *
     * @param  string  $master
     * @param string $domain
     * @return static
     */
    public static function get($master, string $domain = 'localhost')
    {
        $environments = config('horizon.environments');
        $environments = ((is_array($environments)) ? array_map(static function ($group) use ($domain) {
            return ((is_array($group)) ? array_map(static function ($supervisor) use ($domain) {
                return ((is_array($supervisor)) ? ['domain' => $domain] + $supervisor : $supervisor);
            }, $group) : $group);
        }, $environments) : $environments);

        return new static($master, $environments, config('horizon.defaults', []));
    }

    /**
     * Add a supervisor with the given options.
     *
     * @param  BaseSupervisorOptions  $options
     * @return void
     */
    protected function add(BaseSupervisorOptions $options)
    {
        app(HorizonCommandQueue::class)->push(
            MasterSupervisor::commandQueueFor($this->master),
            AddSupervisor::class,
            $options->toArray()
        );
    }

    /**
     * Convert the given array of options into a SupervisorOptions instance.
     *
     * @param  string  $supervisor
     * @param  array  $options
     * @return SupervisorOptions
     */
    protected function convert($supervisor, $options)
    {
        return SupervisorOptions::fromArray(['domain' => $options['domain']] + parent::convert($supervisor, $options)->toArray());
    }
}
