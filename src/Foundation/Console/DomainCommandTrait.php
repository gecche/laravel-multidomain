<?php namespace Gecche\Multidomain\Foundation\Console;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Config;

trait DomainCommandTrait {

    protected function getDomainEnvFilePath()
    {
        return base_path() . DIRECTORY_SEPARATOR . '.env.' . $this->domain;
    }

    protected function getDomainStoragePath($domain = null) {
        if ($domain == null) {
            $domain = $this->domain;
        }

        return storage_path() . DIRECTORY_SEPARATOR . domain_sanitized($domain);
    }

}
