<?php namespace Gecche\Multidomain\Foundation\Bootstrap;

use Illuminate\Contracts\Foundation\Application;

class DetectDomain {

    /**
     * Bootstrap the given application.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @return void
     */
    public function bootstrap(Application $app)
    {
        $domain = $app->detectDomain();

        $this->loadDomainConfig($app, $domain);

        if ($app->domainStoragePath($domain)) {
            $app->useStoragePath($app->domainStoragePath($domain));
        }
    }

    /**
     * Load domain-specific configuration.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @param  string  $domain
     * @return void
     */
    protected function loadDomainConfig(Application $app, $domain)
    {
        $configPath = config_path("domains/{$domain}.php");
        if (file_exists($configPath)) {
            $app->configure($domain);
        }
    }
}