<?php namespace Gecche\Multidomain\Foundation;

use Closure;

class DomainDetector {

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
		//return filter_input(INPUT_SERVER,'SERVER_NAME');
            		return array_get($_SERVER,'SERVER_NAME');

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
		return array_first($args, function($k, $v)
		{
			return starts_with($v, '--domain');
		});
	}

    /*
     * Split the domain name into scheme, name and port
     */
    public function split($domain) {
        if (starts_with($domain,'https://')) {
            $scheme = 'https';
            $domain = substr($domain,8);
        } elseif (starts_with($domain,'http://')) {
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
