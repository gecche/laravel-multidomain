<?php namespace Gecche\Multidomain\Console;

use Symfony\Component\Console\Input\InputOption;

class Application extends \Illuminate\Console\Application {

	/**
	 * Get the default input definitions for the applications.
	 *
	 * @return \Symfony\Component\Console\Input\InputDefinition
	 */
	protected function getDefaultInputDefinition()
	{
		$definition = parent::getDefaultInputDefinition();

		$definition->addOption($this->getDomainOption());

		return $definition;
	}

	/**
	 * Get the global environment option for the definition.
	 *
	 * @return \Symfony\Component\Console\Input\InputOption
	 */
	protected function getDomainOption()
	{
		$message = 'The domain the command should run under.';

		return new InputOption('--domain', null, InputOption::VALUE_OPTIONAL, $message);
	}

       
}
