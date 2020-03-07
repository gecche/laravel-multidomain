<?php

namespace Gecche\Multidomain\Foundation;

use Illuminate\Contracts\Container\BindingResolutionException;

class Application extends \Illuminate\Foundation\Application
{
    /**
     * The environment file to load during bootstrapping.
     *
     * @var string
     */
    protected $environmentFile = null;

    /**
     * Detect the application's current domain.
     *
     * @param  array|string $envs
     * @return string
     */
    public function detectDomain()
    {

        $args = isset($_SERVER['argv']) ? $_SERVER['argv'] : null;

        $domainDetector = new DomainDetector();
        $fullDomain = $domainDetector->detect($args);
        list($domain_scheme, $domain_name, $domain_port) = $domainDetector->split($fullDomain);
        $this['full_domain'] = $fullDomain;
        $this['domain'] = $domain_name;
        $this['domain_scheme'] = $domain_scheme;
        $this['domain_port'] = $domain_port;
        return;
    }

    /**
     * Get or check the current application domain.
     *
     * @return string
     */
    public function domain()
    {
        if (count(func_get_args()) > 0) {
            return in_array($this['domain'], func_get_args());
        }

        return $this['domain'];
    }

    /**
     * Get or check the full current application domain with HTTP scheme and port.
     *
     * @param  mixed
     * @return string
     */
    public function fullDomain()
    {
        if (count(func_get_args()) > 0) {
            return in_array($this['full_domain'], func_get_args());
        }

        return $this['full_domain'];
    }

    /**
     * Get the environment file the application is using.
     *
     * @return string
     */
    public function environmentFile()
    {
        return $this->environmentFile ?: $this->environmentFileDomain();
    }

    /**
     * Get the environment file of the current domain if it exists.
     * The file has to be named .env.<DOMAIN>
     * It returns the base .env file if a specific file does not exist.
     *
     * @return string
     */
    public function environmentFileDomain($domain = null)
    {
        if (is_null($domain)) {
            try {
                $domain = $this['domain'];
            } catch (\Exception $e) {
                $this->detectDomain();
                $domain = $this['domain'];
            }
        }
        $filePath = rtrim($this['path.base'], DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        $file = '.env.' . $domain;
        return file_exists($filePath . $file) ? $file : '.env';
    }

    /**
     * Get the path to the storage directory of the current domain.
     * The storage path is a folder in the main storage laravel folder
     * with the sanitized domain name (dots are replaced with underscores)
     * It is sanitized in order to avoid problems with dots in paths especially
     * in the case of using array_dot notation.
     *
     * @return string
     */
    public function domainStoragePath($domain = null)
    {
        if (is_null($domain)) {
            $domain = $this['domain'];
        }
        $domainPath = domain_sanitized($domain);
        $domainStoragePath = $this->storagePath() . DIRECTORY_SEPARATOR . $domainPath;
        if (file_exists($domainStoragePath))
            return $domainStoragePath;
        return $this->storagePath();
    }

    /**
     * Get the path to the configuration cache file.
     *
     * @return string
     */
    public function getCachedConfigPath()
    {
        return $_ENV['APP_CONFIG_CACHE'] ?? $this->getStandardCachedPath('config');
    }

    /**
     * Get the path to the configuration cache file.
     *
     * @return string
     */
    public function getCachedRoutesPath()
    {
        return $_ENV['APP_ROUTES_CACHE'] ?? $this->getStandardCachedPath('routes');
    }

    /**
     * Get the path to the events cache file.
     *
     * @return string
     */
    public function getCachedEventsPath()
    {
        return $_ENV['APP_EVENTS_CACHE'] ?? $this->getStandardCachedPath('events');
    }

    /**
     * Get the path to the default cache file for config or routes.
     *
     * @param string
     * @return string
     */
    protected function getStandardCachedPath($type)
    {
        $domainSuffix = $this->getDomainCachedFileSuffix();
        return $this->bootstrapPath().'/cache/'.$type.$domainSuffix;
    }

    /**
     * Get a standard suffix for cache files depending upon the loaded .env file
     *
     * @return string
     */
    protected function getDomainCachedFileSuffix()
    {
        $envFile = $this->environmentFile();
        if ($envFile && $envFile == '.env')
            return '.php';
        $envDomainPart = substr($envFile,5);
        return '-'.domain_sanitized($envDomainPart).'.php';
    }

    /*
     * Get the list of installed domains
     *
     * @return Array
     */
    public function domainsList() {

        $domainsInConfig = config('domain.domains',[]);

        $domains = [];

        foreach ($domainsInConfig as $domain) {
            $domains[$domain] = [
                'storage_path' => $this->domainStoragePath($domain),
                'env' => $this->environmentFileDomain($domain),
            ];
        }

        return $domains;

    }
}
