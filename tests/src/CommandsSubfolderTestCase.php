<?php
/**
 * Created by PhpStorm.
 * User: gecche
 * Date: 01/10/2019
 * Time: 11:15
 */

namespace Gecche\Multidomain\Tests;

use Gecche\Multidomain\Foundation\Application;
use Gecche\Multidomain\Foundation\Providers\DomainConsoleServiceProvider;
use Gecche\Multidomain\Tests\Http\Kernel as HttpKernel;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Gecche\Multidomain\Tests\Console\Kernel as ConsoleKernel;

class CommandsSubfolderTestCase extends CommandsTestCase
{

    protected $envPath = 'envs';

    protected $files = null;

    protected $site = 'site1.test';
    protected $siteDbName = 'db_site1';

    /*
     * Added for changes in artisan ouput in Laravel 5.7
     */
    public $mockConsoleOutput = false;

    /**
     * Setup the test environment.
     *
     * Tests need an .env file and the domain.php config file published.
     * This is what we do in the setUp method
     *
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->files = new Filesystem();

        if (!is_dir(env_path('/'.$this->envPath))) {
            mkdir(env_path('/'.$this->envPath));
        }
        copy(__DIR__ . '/../.env.example',env_path($this->envPath.'/.env'));

        $this->artisan('vendor:publish',['--provider' => 'Gecche\Multidomain\Foundation\Providers\DomainConsoleServiceProvider']);
    }

    protected function resolveApplication()
    {
        return tap(new Application($this->getBasePath(),$this->getBasePath().'/'.$this->envPath), function ($app) {
            $app->bind(
                'Illuminate\Foundation\Bootstrap\LoadConfiguration',
                'Orchestra\Testbench\Bootstrap\LoadConfiguration'
            );
        });
    }




}
