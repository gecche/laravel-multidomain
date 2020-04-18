<?php namespace Gecche\Multidomain\Foundation\Console;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Facades\Config;

class UpdateEnvDomainCommand extends GeneratorCommand
{

    use DomainCommandTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'domain:update_env
                            {domain? : The name of the domain to which update the env (if empty all the env domains will be updated)}
                            {--domain_values= : The optional values for the domain variables to be stored in the env file (json object)}';




    protected $description = "Adds a domain to the framework.";
    protected $domain;
    protected $envFiles = [];

    /*
     * Se il file di ambiente esiste già viene semplicemente sovrascirtto con i nuovi valori passati dal comando (update)
     */
    public function handle()
    {
        $this->domain = $this->argument('domain');

        $this->envFiles = $this->getDomainEnvFiles();

        /*
         * CREATE ENV FILE FOR DOMAIN
         */
        $this->updateDomainEnvFiles();

        $this->line("<info>Updated env domain files</info>");
    }


    protected function getDomainEnvFiles()
    {

        if ($this->domain) {
            if ($this->files->exists($this->getDomainEnvFilePath())) {
                return [$this->getDomainEnvFilePath()];
            }
        }

        $envFiles = [
            env_path('.env'),
        ];
        $domainList = Config::get('domain.domains',[]);


        foreach ($domainList as $domain) {
            $domainFile = $this->getDomainEnvFilePath($domain);
            if ($this->files->exists($domainFile)) {
                $envFiles[] = $domainFile;
            }
        }

        return $envFiles;

    }


    protected function updateDomainEnvFiles()
    {
        $domainValues = json_decode($this->option("domain_values"), true);
//        $this->line("<info>".var_dump($this->option("domain_values"))."</info>");
//        $this->line("<info>".var_dump($domainValues)."</info>");

        if (!is_array($domainValues)) {
            $domainValues = array();
        }






        foreach ($this->envFiles as $envFilePath) {
            $envArray = $this->getVarsArray($envFilePath);

            $domainEnvArray = array_merge($envArray, $domainValues);
            $domainEnvFileContents = $this->makeDomainEnvFileContents($domainEnvArray);

            $this->files->put($envFilePath, $domainEnvFileContents);
        }
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {

    }


}
