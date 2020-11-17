<?php
namespace Gecche\Multidomain\Queue;

use Illuminate\Queue\ListenerOptions as BaseListenerOptions;
use Illuminate\Queue\Listener as BaseListener;
use Illuminate\Support\ProcessUtils;
use Symfony\Component\Process\Process;

/**
 * Class Listener
 *
 * @package Gecche\Multidomain\Queue
 */
class Listener extends BaseListener
{
    /**
     * Create a new Symfony process for the worker.
     *
     * @param  string  $connection
     * @param  string  $queue
     * @return Process
     */
    public function makeProcess($connection, $queue, BaseListenerOptions $options)
    {
        $command = $this->createCommand(
            $connection,
            $queue,
            $options
        );

        if ($options instanceof ListenerOptions && $options->domain !== null) {
            $command = $this->addDomain($command, $options);
        }

        // If the environment is set, we will append it to the command string so the
        // workers will run under the specified environment. Otherwise, they will
        // just run under the production environment which is not always right.
        if (isset($options->environment)) {
            $command = $this->addEnvironment($command, $options);
        }

        return new Process(
            $command, $this->commandPath, null, null, $options->timeout
        );
    }

    /**
     * Add the domain option to the given command.
     *
     * @param  array  $command
     * @param  ListenerOptions  $options
     * @return array
     */
    protected function addDomain(array $command, ListenerOptions $options) : array
    {
        return array_merge($command, ["--domain={$options->domain}"]);
    }
}
