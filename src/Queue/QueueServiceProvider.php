<?php namespace Gecche\Multidomain\Queue;

use Gecche\Multidomain\Queue\Console\ListenCommand as QueueListenCommand;

class QueueServiceProvider extends \Illuminate\Queue\QueueServiceProvider {


	/**
	 * Register the queue listener.
	 *
	 * @return void
	 */
	protected function registerListener()
	{
        $this->app->singleton('queue.listener', function () {
            return new Listener($this->app->basePath());
        });
	}

    /**
     * Extends the queue listen command
     *
     * @return void
     */
    public function boot()
    {

        $this->app->singleton('command.queue.listen', function ($app) {
            return new QueueListenCommand($app['queue.listener']);
        });
    }

}
