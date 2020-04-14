<?php namespace Gecche\Multidomain\Foundation\Console;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Arr;


class AddDomainCommand extends GeneratorCommand
{

    use DomainCommandTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'domain:add
                            {domain : The name of the domain to add to the framework}
                            {--domain_values= : The optional values for the domain variables to be stored in the env file (json object)}
                            {--force : Force the creation of domain storage dirs also if they already exist}';


    protected $description = "Adds a domain to the framework by creating the specific .env file and storage dirs.";
    protected $domain;

    /*
     * Se il file di ambiente esiste già viene semplicemente sovrascirtto con i nuovi valori passati dal comando (update)
     */
    public function handle()
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
        return env_path(Config::get('domain.env_stub', '.env'));
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
        $domains = Arr::get($config, 'domains', []);
        if (!array_key_exists($this->domain, $domains)) {
            $domains[$this->domain] = $this->domain;
        }

        ksort($domains);
        $config['domains'] = $domains;

        return $config;
    }





}
