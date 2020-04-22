<?php

namespace Gecche\Multidomain\Foundation;

use Illuminate\Support\Env;

class Application extends \Illuminate\Foundation\Application
{
    /**
     * The environment file to load during bootstrapping.
     *
     * @var string
     */
    protected $environmentFile = null;

    /**
     * @var bool
     *
     * False is the domain has never been detected
     */
    protected $domainDetected = false;

    /**
     * Create a new application instance.
     * @param  string|null  $basePath
     * @param  string|null  $environmentPath
     */
    public function __construct($basePath = null, $environmentPath = null)
    {
        $environmentPath = $environmentPath ?? $basePath;
        $this->useEnvironmentPath(rtrim($environmentPath,'\/'));

        parent::__construct($basePath);
    }

    /**
     * Detect the application's current domain.
     *
     * @param array|string $envs
     * @return void;
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

        $this->domainDetected = true;
        return;
    }


    /**
     * Force the detection of the domain if it has never been detected.
     * It should not happens in standard flow.
     *
     * @return void;
     */
    protected function checkDomainDetection()
    {
        if (!$this->domainDetected)
            $this->detectDomain();
        return;
    }

    /**
     * Get or check the current application domain.
     *
     * @return string
     */
    public function domain()
    {

        $this->checkDomainDetection();

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
        $this->checkDomainDetection();

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
        $this->checkDomainDetection();

        if (is_null($domain)) {
            $domain = $this['domain'];
        }
        $file = '.env.' . $domain;
        return ($domain && file_exists(env_path($file))) ? $file : '.env';
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

        $this->checkDomainDetection();

        if (is_null($domain)) {
            $domain = $this['domain'];
        }
        $domainPath = domain_sanitized($domain);
        $domainStoragePath = rtrim($this->storagePath() . DIRECTORY_SEPARATOR . $domainPath,"\/");
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
        return $this->normalizeCacheDomainPath('APP_CONFIG_CACHE', 'cache/config.php');
    }

    /**
     * Get the path to the configuration cache file.
     *
     * @return string
     */
    public function getCachedRoutesPath()
    {
        return $this->normalizeCacheDomainPath('APP_ROUTES_CACHE', 'cache/routes.php');
    }

    /**
     * Get the path to the events cache file.
     *
     * @return string
     */
    public function getCachedEventsPath()
    {
        return $this->normalizeCacheDomainPath('APP_EVENTS_CACHE', 'cache/events.php');
    }

    /**
     * Get the path to the default cache file for config or routes.
     *
     * @param string
     * @return string
     */
    protected function normalizeCacheDomainPath($key, $default)
    {

        if (is_null($env = Env::get($key))) {
            $domainDefault = $this->getDomainCachedFileDefault($default);
            return $this->bootstrapPath($domainDefault);
        }

        return Str::startsWith($env, '/')
            ? $env
            : $this->basePath($env);

    }

    /**
     * Get a default file for cache files depending upon the loaded .env file
     *
     * @return string
     */
    protected function getDomainCachedFileDefault($default)
    {
        $envFile = $this->environmentFile();
        if ($envFile && $envFile == '.env')
            return $default;

        $this->checkDomainDetection();

        $defaultWithoutPhpExt = substr($default,0,-4);

        return $defaultWithoutPhpExt.'-'.domain_sanitized($this['domain']).'.php';
    }


    /*
     * Get the list of installed domains
     *
     * @return Array
     */
    public function domainsList()
    {

        $domainsInConfig = config('domain.domains', []);

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
