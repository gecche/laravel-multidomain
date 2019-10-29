<?php namespace Gecche\Multidomain\Tests\Console\Commands;

use Illuminate\Bus\Queueable;
use Illuminate\Console\Command;


class NameCommand extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'name';


    protected $description = "Display the app name in the env";

    /*
     * Se il file di ambiente esiste già viene semplicemente sovrascirtto con i nuovi valori passati dal comando (update)
     */
    public function handle()
    {
        echo env('APP_NAME');
    }







}
