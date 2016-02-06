<?php namespace Gecche\Multidomain;

use App;
use Gecche\Multidomain\Foundation\Console\RemoveDomainCommand;
use Illuminate\Support\ServiceProvider;
use Gecche\Multidomain\Foundation\Console\DomainCommand;
use Gecche\Multidomain\Foundation\Console\AddDomainCommand;

class ConsoleServiceProvider extends ServiceProvider {

    protected $defer = false;


    /**
     * The commands to be registered.
     *
     * @var array
     */
    protected $commands = [
        'Domain',
        'AddDomain',
        'RemoveDomain'
    ];


    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->alias('artisan','\Gecche\Multidomain\Console\Application');

        foreach ($this->commands as $command)
        {
            $this->{"register{$command}Command"}();
        }

        $this->commands(
            "command.domain",
            "command.domain.add",
            "command.domain.remove"
        );
    }


    public function boot() {

    }


    /**
     * Register the command.
     *
     * @return void
     */
    protected function registerDomainCommand()
    {
        $this->app->singleton('command.domain', function()
        {
            return new DomainCommand;
        });
    }

    /**
     * Register the command.
     *
     * @return void
     */
    protected function registerAddDomainCommand()
    {
        $this->app->singleton('command.domain.add', function($app)
        {
            return new AddDomainCommand($app['files']);
        });
    }

    /**
     * Register the command.
     *
     * @return void
     */
    protected function registerRemoveDomainCommand()
    {
        $this->app->singleton('command.domain.remove', function($app)
        {
            return new RemoveDomainCommand($app['files']);
        });
    }

}
