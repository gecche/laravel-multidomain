<?php namespace Gecche\Multidomain\Queue;

use Illuminate\Queue\ListenerOptions;
use Illuminate\Support\ProcessUtils;
use Symfony\Component\Process\Process;

class Listener extends \Illuminate\Queue\Listener{




	/**
	 * Create a new Symfony process for the worker.
	 *
	 * @param  string  $connection
	 * @param  string  $queue
	 * @param  int     $delay
	 * @param  int     $memory
	 * @param  int     $timeout
	 * @return \Symfony\Component\Process\Process
	 */
	public function makeProcess($connection, $queue, ListenerOptions $options)
	{
        $command = $this->createCommand(
            $connection,
            $queue,
            $options
        );

        if (isset($options->domain)) {
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
     * @param  string  $command
     * @param  \Gecche\Multidomain\Queue\ListenerOptions  $options
     * @return string
     */
    protected function addDomain($command, ListenerOptions $options)
    {
        return array_merge($command, ["--domain={$options->domain}"]);
    }



}
