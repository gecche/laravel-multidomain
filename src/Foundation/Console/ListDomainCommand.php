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
    protected $signature = 'domain:list
                            {--output=txt : the output type json or txt (txt as default)}';

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
        $outputType = $this->option('output');
        $domains = $this->buildResult($domains);
        switch (strtolower(trim($outputType ?? 'txt'))) {
            default:
            case 'txt':
                $this->outputAsText($domains);
                break;
            case 'table':
                $this->outputAsTable($domains);
                break;
            case 'json':
                $this->outputAsJson($domains);
                break;
        }
    }

    protected function outputAsText(array $domains)
    {
        foreach ($domains as $domain) {
            $this->line("<info>Domain: </info><comment>" . Arr::get($domain,'domain') . "</comment>");

            $this->line("<info> - Storage dir: </info><comment>" . Arr::get($domain,'storage_dir') . "</comment>");
            $this->line("<info> - Env file: </info><comment>" . Arr::get($domain,'env_file') . "</comment>");

            $this->line("");

        }
    }

    protected function outputAsJson(array $domains)
    {
        $this->output->writeln(json_encode($domains));
    }

    protected function outputAsTable(array $domains)
    {
        $this->output->table(array_keys(head($domains)), $domains);
    }

    protected function buildResult(array $domains): array
    {
        $result = [];
        foreach ($domains as $domain) {
            $result [] = [
                'domain' => $domain,
                'storage_dir' => $this->getDomainStoragePath($domain),
                'env_file' => $this->getDomainEnvFilePath($domain),
            ];
        }

        return $result;
    }
}
