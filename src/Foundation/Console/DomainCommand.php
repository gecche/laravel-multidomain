<?php namespace Gecche\Multidomain\Foundation\Console;

use Illuminate\Console\Command;

class DomainCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
    protected $signature = 'domain';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = "Display the current framework domain";

	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	public function handle()
	{
		$this->line('<info>Current application domain:</info> <comment>'.$this->laravel['domain'].  "--" . $this->laravel['domain_port'] . "--" . $this->laravel['domain_scheme'].'</comment>');
	}

}
