<?php namespace Gecche\Multidomain\Foundation\Http;

use Exception;
use Illuminate\Routing\Router;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\Facade;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\TerminableMiddleware;
use Illuminate\Contracts\Http\Kernel as KernelContract;

class Kernel extends \Illuminate\Foundation\Http\Kernel {
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
        \Illuminate\Foundation\Bootstrap\RegisterProviders::class,
        \Illuminate\Foundation\Bootstrap\BootProviders::class,
    ];

}
