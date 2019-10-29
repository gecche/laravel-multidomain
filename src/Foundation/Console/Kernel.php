<?php namespace Gecche\Multidomain\Foundation\Console;

use Gecche\Multidomain\Console\Application as Artisan;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;


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


    /**
     * Run an Artisan console command by name.
     *
     * @param  string  $command
     * @param  array  $parameters
     * @param  \Symfony\Component\Console\Output\OutputInterface  $outputBuffer
     * @return int
     */
    public function call($command, array $parameters = [], $outputBuffer = null, $forceBootstrap = false)
    {
        if ($forceBootstrap) {
            $argv = isset($_SERVER['argv']) ? $_SERVER['argv'] : [];
            $argvDomain = Arr::first($argv, function ($value) {
                return Str::startsWith($value, '--domain');
            });
            echo $argvDomain . "----";
            if (!$argvDomain) {
                $paramDomain = Arr::get($parameters,'--domain');
                echo $paramDomain . "++++";
                if ($paramDomain) {

                    $_SERVER['argv'][] = $paramDomain;
                }
            }
            $this->app->bootstrapWith($this->bootstrappers());
        }

        $this->bootstrap();

        return $this->getArtisan()->call($command, $parameters, $outputBuffer);
    }

}
