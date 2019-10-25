<?php

namespace Gecche\Multidomain\Tests\Console;

use Gecche\Multidomain\Tests\Console\Commands\NameCommand;
use Gecche\Multidomain\Tests\Console\Commands\QueuePushCommand;
use Illuminate\Console\Scheduling\Schedule;
use Gecche\Multidomain\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        NameCommand::class,
        QueuePushCommand::class,
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        //require base_path('routes/console.php');
    }
}
