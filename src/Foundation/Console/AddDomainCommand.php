<?php namespace Gecche\Multidomain\Foundation\Console;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Config;

class AddDomainCommand extends GeneratorCommand
{

    use DomainCommandTrait;

    protected $name = "domain:add";
    protected $description = "Adds a domain to the framework.";
    protected $domain;

    /*
     * Se il file di ambiente esiste già viene semplicemente sovrascirtto con i nuovi valori passati dal comando (update)
     */
    public function fire()
    {
        $this->domain = $this->argument('domain');

        /*
         * CREATE ENV FILE FOR DOMAIN
         */
        $this->createDomainEnvFile();


        /*
         * Setting domain storage directories
         */

        $this->createDomainStorageDirs();

        /*
         * Update config file
         */

        $this->updateConfigFile();

        $this->line("<info>Added</info> <comment>" . $this->domain . "</comment> <info>to the application.</info>");
    }


    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        if ($this->files->exists($this->getDomainEnvFilePath())) {
            return $this->getDomainEnvFilePath();
        }
        return base_path() . DIRECTORY_SEPARATOR . Config::get('domain.env_stub', '.env');
    }

    protected function createDomainEnvFile()
    {
        $envFilePath = $this->getStub();

        $domainValues = json_decode($this->option("domain_values"), true);


        if (!is_array($domainValues)) {
            $domainValues = array();
        }


        $envArray = $this->getVarsArray($envFilePath);

        $domainEnvFilePath = $this->getDomainEnvFilePath();

        $domainEnvArray = array_merge($envArray, $domainValues);
        $domainEnvFileContents = $this->makeDomainEnvFileContents($domainEnvArray);

        $this->files->put($domainEnvFilePath, $domainEnvFileContents);
    }

    protected function getVarsArray($path)
    {
        $envFileContents = $this->files->getArray($path);
        $varsArray = array();
        foreach ($envFileContents as $line) {
            $lineArray = explode('=', $line);

            if (count($lineArray) !== 2) {
                continue;
            }

            $varsArray[$lineArray[0]] = trim($lineArray[1]);

        }
        return $varsArray;
    }

    protected function makeDomainEnvFileContents($domainValues)
    {
        $contents = '';
        $previousKeyPrefix = '';
        foreach ($domainValues as $key => $value) {
            $keyPrefix = current(explode('_', $key));
            if ($keyPrefix !== $previousKeyPrefix && !empty($contents)) {
                $contents .= "\n";
            }
            $contents .= $key . '=' . $value . "\n";
            $previousKeyPrefix = $keyPrefix;
        }
        return $contents;
    }

    public function createDomainStorageDirs()
    {
        $storageDirs = Config::get('domain.storage_dirs', array());
        $path = $this->getDomainStoragePath($this->domain);
        $rootPath = storage_path();
        if ($this->files->exists($path) && !$this->option('force')) {
            return;
        }

        if ($this->files->exists($path)) {
            $this->files->deleteDirectory($path);
        }


        $this->createRecursiveDomainStorageDirs($rootPath, $path, $storageDirs);


    }

    protected function createRecursiveDomainStorageDirs($rootPath, $path, $dirs)
    {
        $this->files->makeDirectory($path, 0755, true);
        foreach (['.gitignore', '.gitkeep'] as $gitFile) {
            $rootGitPath = $rootPath . DIRECTORY_SEPARATOR . $gitFile;
            if ($this->files->exists($rootGitPath)) {
                $gitPath = $path . DIRECTORY_SEPARATOR . $gitFile;
                $this->files->copy($rootGitPath, $gitPath);
            }
        }
        foreach ($dirs as $subdir => $subsubdirs) {
            $fullPath = $path . DIRECTORY_SEPARATOR . $subdir;
            $fullRootPath = $rootPath . DIRECTORY_SEPARATOR . $subdir;
            $this->createRecursiveDomainStorageDirs($fullRootPath, $fullPath, $subsubdirs);
        }

    }

    protected function addDomainToConfigFile($config) {
        $domains = array_get($config, 'domains', []);
        if (!array_key_exists($this->domain, $domains)) {
            $domains[$this->domain] = $this->domain;
        }

        ksort($domains);
        $config['domains'] = $domains;

        return $config;
    }

    protected function getArguments()
    {
        return [
            ["domain", InputArgument::REQUIRED, "The name of the domain to add to the framework."],
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
            ],
            [
                "force",
                null,
                InputOption::VALUE_NONE,
                "Force the creation of domain storage dirs also if they already exist"
            ],
        ];
    }


}
