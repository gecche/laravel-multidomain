<?php namespace Gecche\Multidomain\Foundation\Bootstrap;

use Illuminate\Contracts\Foundation\Application;

class LoadDomainConfiguration {

    /**
     * Bootstrap the given application.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @return void
     */
    public function bootstrap(Application $app)
    {
        $domain = $app->domain();
        $configPath = $app->configPath('domains' . DIRECTORY_SEPARATOR . domain_sanitized($domain) . '.php');

        if (! file_exists($configPath)) {
            return;
        }

        $domainConfig = require $configPath;

        if (! is_array($domainConfig)) {
            return;
        }

        $config = $app->make('config');

        foreach ($domainConfig as $key => $value) {
            $existing = $config->get($key);

            if (is_array($existing) && is_array($value)) {
                $config->set($key, array_replace_recursive($existing, $value));
            } else {
                $config->set($key, $value);
            }
        }
    }

}
