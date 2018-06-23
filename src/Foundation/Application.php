<?php

namespace Gecche\Multidomain\Foundation;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Cookie;

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
    public function environmentFileDomain()
    {
        $filePath = rtrim($this['path.base'], DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        $file = '.env.' . $this['domain'];
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
    public function domainStoragePath()
    {
        $domainPath = domain_sanitized($this['domain']);
        $domainStoragePath = $this->storagePath() . DIRECTORY_SEPARATOR . $domainPath;
        if (file_exists($domainStoragePath))
            return $domainStoragePath;
        return $this->storagePath();
    }


}
