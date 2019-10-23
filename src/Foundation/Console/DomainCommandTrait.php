<?php namespace Gecche\Multidomain\Foundation\Console;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Config;

trait DomainCommandTrait {

    protected $configFile = 'domain';

    protected function getDomainEnvFilePath($domain = null)
    {
        if (is_null($domain)) {
            $domain = $this->domain;
        }
        return base_path() . DIRECTORY_SEPARATOR . '.env.' . $domain;
    }

    protected function getDomainStoragePath($domain = null) {
        if ($domain == null) {
            $domain = $this->domain;
        }

        return storage_path() . DIRECTORY_SEPARATOR . domain_sanitized($domain);
    }

    protected function getConfigStub() {
        $filename = base_path('stubs/domain/config.stub');

        if (!$this->files->exists($filename)) {
            $filename = __DIR__ . '/../../stubs/config.stub';
        }

        return $this->files->get($filename);
    }

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
            ["\narray (","\n  array (","\n    array (","\n      array ("], '[', $modelConfigStub
        );
        $modelConfigStub = str_replace(
            ["),"], "],", $modelConfigStub
        );

        $this->files->put($filename, $modelConfigStub);
        Config::set($this->configFile,$finalConfig);
    }

    protected function getVarsArray($path)
    {
        $envFileContents = $this->files->get($path);
        $envFileContentsArray = explode("\n",$envFileContents);
        $varsArray = array();
        foreach ($envFileContentsArray as $line) {
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



}
