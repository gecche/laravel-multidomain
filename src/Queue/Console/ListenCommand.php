<?php namespace Gecche\Multidomain\Queue\Console;


class ListenCommand extends \Illuminate\Queue\Console\ListenCommand {


	/**
	 * Set the options on the queue listener.
	 *
	 * @return void
	 */
	protected function setListenerOptions()
	{
		$this->listener->setDomain($this->laravel->fullDomain());
        parent::setListenerOptions();
	}


}
