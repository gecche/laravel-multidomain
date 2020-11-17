<?php
namespace Gecche\Multidomain\Queue;

use Gecche\Multidomain\Queue\Console\ListenCommand as QueueListenCommand;
use Illuminate\Queue\QueueServiceProvider as BaseQueueServiceProvider;

/**
 * Class QueueServiceProvider
 *
 * @package Gecche\Multidomain\Queue
 */
class QueueServiceProvider extends BaseQueueServiceProvider
{
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
    public function register()
    {
        parent::register();

        $this->app->extend('command.queue.listen', function ($command, $app) {
            return new QueueListenCommand($app['queue.listener']);
        });
    }
}
