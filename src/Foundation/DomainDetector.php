<?php namespace Gecche\Multidomain\Foundation;

use Closure;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class DomainDetector {

    /**
     * @var Closure|null
     *
     * Function for customizing the domain detection process in the web scenario
     */
    protected $domainDetectionFunctionWeb = false;


    /**
     * DomainDetector constructor.
     */
    public function __construct($domainDetectionFunctionWeb = null)
    {

        $this->domainDetectionFunctionWeb = $domainDetectionFunctionWeb;

    }


	/**
	 * Detect the application's current environment.
	 *
	 * @param  array|string  $environments
	 * @param  array|null  $consoleArgs
	 * @return string
	 */
	public function detect($consoleArgs = null)
	{
        if ($consoleArgs)
		{
			return $this->detectConsoleDomain($consoleArgs);
		}
		else
		{
			return $this->detectWebDomain();
		}
	}

	/**
	 * Set the application environment for a web request.
	 *
	 * @param  array|string  $environments
	 * @return string
	 */
	protected function detectWebDomain()
	{
	    if ($this->domainDetectionFunctionWeb instanceof Closure) {
	        return ($this->domainDetectionFunctionWeb)();
        }
		//return filter_input(INPUT_SERVER,'SERVER_NAME');
            		return Arr::get($_SERVER,'SERVER_NAME');

	}

	/**
	 * Set the application environment from command-line arguments.
	 *
	 * @param  mixed   $environments
	 * @param  array  $args
	 * @return string
	 */
	protected function detectConsoleDomain(array $args)
	{
		// First we will check if an environment argument was passed via console arguments
		// and if it was that automatically overrides as the environment. Otherwise, we
		// will check the environment as a "web" request like a typical HTTP request.
		if ( ! is_null($value = $this->getDomainArgument($args)))
		{
			return head(array_slice(explode('=', $value), 1));
		}
		else
		{
			return $this->detectWebDomain();
		}
	}

	/**
	 * Get the environment argument from the console.
	 *
	 * @param  array  $args
	 * @return string|null
	 */
	protected function getDomainArgument(array $args)
	{
        return Arr::first($args, function ($value) {
            return Str::startsWith($value, '--domain');
        });
	}

    /*
     * Split the domain name into scheme, name and port
     */
    public function split($domain) {
        if (Str::startsWith($domain,'https://')) {
            $scheme = 'https';
            $domain = substr($domain,8);
        } elseif (Str::startsWith($domain,'http://')) {
            $scheme = 'http';
            $domain = substr($domain,7);
        } else {
            $scheme = 'http';
        }

        $semicolon = strpos($domain,':');
        if ($semicolon === false) {
            $port = ($scheme == 'http') ? 80 : 443;
        } else {
            $port = substr($domain,$semicolon+1);
            $domain = substr($domain,0,-(strlen($port)+1));
        }

        return array($scheme,$domain,$port);

    }

}
