<?php namespace Gecche\Multidomain\Foundation\Console;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Config;

trait DomainCommandTrait
{

    /**
     * The name of the configuration file of the package
     * @var string
     */
    protected $configFile = 'domain';

    /**
     * Returns the path of the .env file for the specified domain
     * @param null $domain
     * @return string
     */
    protected function getDomainEnvFilePath($domain = null)
    {
        if (is_null($domain)) {
            $domain = $this->domain;
        }

        return rtrim(env_path('.env.' . $domain),'.');
    }

    /**
     * Returns the path of the storage folder for the specified domain
     * @param null $domain
     * @return string
     */
    protected function getDomainStoragePath($domain = null)
    {
        $path = app()->exactDomainStoragePath($domain);
        return $path;
    }

    /**
     * Returns the contents of the stub of the package's configuration file
     * @return mixed
     */
    protected function getConfigStub()
    {
        $filename = base_path('stubs/domain/config.stub');

        if (!$this->files->exists($filename)) {
            $filename = __DIR__ . '/../../stubs/config.stub';
        }

        return $this->files->get($filename);
    }

    /**
     * This method updates the package's config file by adding or removing the domain handled by the caller command.
     * It calls either the addDomainToConfigFile or the removeDomainToConfigFile method of the caller.
     *
     * @param string $opType (add|remove)
     */
    protected function updateConfigFile($opType = 'add')
    {
        $filename = base_path('config/' . $this->configFile . '.php');

        $config = include $filename;

        $configStub = $this->getConfigStub();

        $methodName = $opType . 'DomainToConfigFile';

        $finalConfig = call_user_func_array([$this, $methodName], [$config]);

        $modelConfigStub = str_replace(
            '{{$configArray}}', var_export($finalConfig, true), $configStub
        );

        $modelConfigStub = str_replace(
            'return array (', 'return [', $modelConfigStub
        );
        $modelConfigStub = str_replace(
            ');', ' ];', $modelConfigStub
        );
        $modelConfigStub = str_replace(
            ["\narray (", "\n  array (", "\n    array (", "\n      array ("], '[', $modelConfigStub
        );
        $modelConfigStub = str_replace(
            ["),"], "],", $modelConfigStub
        );

        $this->files->put($filename, $modelConfigStub);
        Config::set($this->configFile, $finalConfig);
    }

    /**
     * This method gets the contents of a file formatted as a standard .env file
     * i.e. with each line in the form of KEY=VALUE
     * and returns the entries as an array
     *
     * @param $path
     * @return array
     */
    protected function getVarsArray($path)
    {
        $envFileContents = $this->files->get($path);
        $envFileContentsArray = explode("\n", $envFileContents);
        $varsArray = array();
        foreach ($envFileContentsArray as $line) {
            $lineArray = explode('=', $line);

            //Skip the line if there is no '='
            if (count($lineArray) < 2) {
                continue;
            }

            $value = substr($line, strlen($lineArray[0])+1);
            $varsArray[$lineArray[0]] = trim($value);

        }
        return $varsArray;
    }

    /**
     * This method prepares the values of an .env file to be stored
     * @param $domainValues
     * @return string
     */
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


}
