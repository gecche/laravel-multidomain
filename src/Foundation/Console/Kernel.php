<?php namespace Gecche\Multidomain\Foundation\Console;

use Gecche\Multidomain\Console\Application as Artisan;


class Kernel extends \Illuminate\Foundation\Console\Kernel {

	/**
	 * The bootstrap classes for the application.
	 *
	 * @var array
	 */
    protected $bootstrappers = [
        \Gecche\Multidomain\Foundation\Bootstrap\DetectDomain::class,
        \Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables::class,
        \Illuminate\Foundation\Bootstrap\LoadConfiguration::class,
        \Illuminate\Foundation\Bootstrap\HandleExceptions::class,
        \Illuminate\Foundation\Bootstrap\RegisterFacades::class,
        \Illuminate\Foundation\Bootstrap\SetRequestForConsole::class,
        \Illuminate\Foundation\Bootstrap\RegisterProviders::class,
        \Illuminate\Foundation\Bootstrap\BootProviders::class,
    ];


    /**
     * Get the Artisan application instance.
     *
     * @return \Illuminate\Console\Application
     */
    protected function getArtisan()
    {
        if (is_null($this->artisan))
        {
            return $this->artisan = (new Artisan($this->app, $this->events, $this->app->version()))
                ->resolveCommands($this->commands);
        }

        return $this->artisan;
    }

}
