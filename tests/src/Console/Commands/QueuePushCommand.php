<?php namespace Gecche\Multidomain\Tests\Console\Commands;

use Gecche\Multidomain\Tests\Jobs\AppNameJob;
use Illuminate\Bus\Queueable;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Queue;


class QueuePushCommand extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'queue_push';


    protected $description = "Push the 'app_name' job onto the queue";

    /*
     * Se il file di ambiente esiste già viene semplicemente sovrascirtto con i nuovi valori passati dal comando (update)
     */
    public function handle()
    {
        AppNameJob::dispatch();
    }







}
