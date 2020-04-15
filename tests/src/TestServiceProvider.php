<?php namespace Gecche\Multidomain\Tests;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

/**
 * Class TestServiceProvider
 *
 * @package Cviebrock\EloquentSluggable
 */
class TestServiceProvider extends BaseServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {

        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        $this->loadMigrationsFrom(
            __DIR__ . '/../database/migrations'
        );
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'multidomain');
    }
}
