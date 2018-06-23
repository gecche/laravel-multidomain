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
        $command = $this->workerCommand;

        if (isset($options->domain)) {
            $command = $this->addDomain($command, $options);
        }

        // If the environment is set, we will append it to the command string so the
        // workers will run under the specified environment. Otherwise, they will
        // just run under the production environment which is not always right.
        if (isset($options->environment)) {
            $command = $this->addEnvironment($command, $options);
        }

        // Next, we will just format out the worker commands with all of the various
        // options available for the command. This will produce the final command
        // line that we will pass into a Symfony process object for processing.
        $command = $this->formatCommand(
            $command, $connection, $queue, $options
        );

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
        return $command.' --domain='.ProcessUtils::escapeArgument($options->domain);
    }



}
