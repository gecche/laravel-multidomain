<?php namespace Gecche\Multidomain\Foundation\Console;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Illuminate\Support\Facades\Config;

class UpdateEnvDomainCommand extends GeneratorCommand
{

    use DomainCommandTrait;

    protected $name = "domain:update_env";
    protected $description = "Adds a domain to the framework.";
    protected $domain;
    protected $envFiles = [];

    /*
     * Se il file di ambiente esiste già viene semplicemente sovrascirtto con i nuovi valori passati dal comando (update)
     */
    public function fire()
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
            base_path() . DIRECTORY_SEPARATOR.'.env',
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

    protected function getArguments()
    {
        return [
            ["domain", InputArgument::OPTIONAL, "The name of the domain to which update the env (if empty all the env domains will be updated)."],
//            ["key", InputArgument::REQUIRED, "The name of the env key to be updated."],
//            ["value", InputArgument::REQUIRED, "The value of the env key to be updated."],
        ];
    }

    protected function getOptions()
    {
        return [
            [
                "domain_values",
                null,
                InputOption::VALUE_OPTIONAL,
                "The optional values for the domain variables (json object).",
                "{}"
            ]
        ];
    }


}
