<?php namespace Gecche\Multidomain\Queue;

use Gecche\Multidomain\Queue\Console\ListenCommand;

class QueueServiceProvider extends \Illuminate\Queue\QueueServiceProvider {


	/**
	 * Register the queue listener.
	 *
	 * @return void
	 */
	protected function registerListener()
	{
		$this->registerListenCommand();

		$this->app->singleton('queue.listener', function($app)
		{
			return new Listener($app['path.base']);
		});
	}

    /**
     * Register the queue listener console command.
     *
     * @return void
     */
    protected function registerListenCommand()
    {
        $this->app->singleton('command.queue.listen', function($app)
        {
            return new ListenCommand($app['queue.listener']);
        });

        $this->commands('command.queue.listen');
    }

}
