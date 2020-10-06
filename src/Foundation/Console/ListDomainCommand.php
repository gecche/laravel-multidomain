<?php namespace Gecche\Multidomain\Foundation\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Arr;

class ListDomainCommand extends Command
{

    use DomainCommandTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'domain:list';


    protected $description = "Lists domains installed in the application.";

    protected $domain;

    /*
     * Se il file di ambiente esiste già viene semplicemente sovrascirtto con i nuovi valori passati dal comando (update)
     */
    public function handle()
    {
        /*
         * GET CONFIG FILE
         */

        $filename = base_path('config/' . $this->configFile . '.php');

        $config = include $filename;

        /*
         * GET DOMAINS BASED ON domains KEY IN THE CONFIG FILE
         */
        $domains = Arr::get($config, 'domains', []);


        /*
         * Simply returns the info for each domain found in config.
         */
        foreach ($domains as $domain) {
            $this->line("<info>Domain: </info><comment>" . $domain . "</comment>");

            $this->line("<info> - Storage dir: </info><comment>" . $this->getDomainStoragePath($domain) . "</comment>");
            $this->line("<info> - Env file: </info><comment>" . $this->getDomainEnvFilePath($domain) . "</comment>");

            $this->line("");

        }


    }

}
