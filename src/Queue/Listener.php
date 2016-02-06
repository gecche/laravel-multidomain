<?php namespace Gecche\Multidomain\Queue;

use Closure;
use Symfony\Component\Process\Process;

class Listener extends \Illuminate\Queue\Listener{

	/**
	 * The domain the workers should run under.
	 *
	 * @var string
	 */
	protected $domain;


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
	public function makeProcess($connection, $queue, $delay, $memory, $timeout)
	{
		$string = $this->workerCommand;

		// If the environment is set, we will append it to the command string so the
		// workers will run under the specified environment. Otherwise, they will
		// just run under the production environment which is not always right.
		if (isset($this->environment))
		{
			$string .= ' --env='.$this->environment;
		}
                
		if (isset($this->domain))
		{
			$string .= ' --domain='.$this->domain;
		}

		// Next, we will just format out the worker commands with all of the various
		// options available for the command. This will produce the final command
		// line that we will pass into a Symfony process object for processing.
		$command = sprintf(
			$string, $connection, $queue, $delay,
			$memory, $this->sleep, $this->maxTries
		);

		return new Process($command, $this->commandPath, null, null, $timeout);
	}

	/**
	 * Get the current listener environment.
	 *
	 * @return string
	 */
	public function getDomain()
	{
		return $this->domain;
	}

	/**
	 * Set the current environment.
	 *
	 * @param  string  $environment
	 * @return void
	 */
	public function setDomain($domain)
	{
		$this->domain = $domain;
	}

}
