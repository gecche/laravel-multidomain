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
use Gecche\Multidomain\Tests\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;


/*
 * HTTPTestCase
 *
 * Http tests for multiple domains should have a distinct behaviour depending upon the chosen domain.
 * In the package the domain is obtained checking the $_SERVER['SERVER_NAME'] superglobal.
 *
 * Here we provide a default setting (e.g. localhost) and two added domains, namely site1.test and site2.test
 *
 * In order ot simulate all the cases, we should run the tests three times with three distinct commands:
 *
 * 1 - ../../../vendor/bin/phpunit (default setting)
 * 2 - SERVER_NAME=site1.test ../../../vendor/bin/phpunit
 * 3 - SERVER_NAME=site2.test ../../../vendor/bin/phpunit
 *
 */

class HttpTestCase extends \Orchestra\Testbench\BrowserKit\TestCase
{

    protected $serverName;
    protected $laravelAppPath = null;


    protected $files = null;

    protected $site1 = 'site1.test';
    protected $site2 = 'site2.test';
    protected $subSite1 = 'sub1.site1.test';
    protected $subSite2 = 'sub2.site2.test';
    protected $siteDbName1 = 'site1';
    protected $siteDbName2 = 'site2';
    protected $subSiteDbName1 = 'subsite1';
    protected $siteAppName1 = 'APPSite1';
    protected $siteAppName2 = 'APPSite2';
    protected $subSiteAppName1 = 'APPSubSite1';

    protected $initialServerGlobal = [];
    protected $initialEnvGlobal = [];

    /**
     * Setup the test environment.
     *
     * First, we provide the .env file and we publish the domain.php config file.
     * Then we use artisna commands in order to provide two new domains, namely site1.test and site2.test
     * and for each one we customize the APP_NAME and the DB_DATABASE entries in their .env files.
     *
     * @return void
     */
    protected function setUp(): void
    {
        $this->laravelAppPath = __DIR__ . '/../../vendor/orchestra/testbench-core/laravel';
        copy(__DIR__ . '/../artisan',$this->laravelAppPath.'/artisan');
        $process = new Process(['php', $this->laravelAppPath.'/artisan', 'config:clear']);
        $process->run();


        $this->files = new Filesystem();
        copy($this->laravelAppPath.'/config/app.php',$this->laravelAppPath.'/config/appORIG.php');
        copy(__DIR__ . '/../config/app.php',$this->laravelAppPath.'/config/app.php');
        copy(__DIR__ . '/../.env.example', $this->laravelAppPath.'/.env');

        $process = new Process(['php', $this->laravelAppPath.'/artisan', 'vendor:publish', '--provider="Gecche\Multidomain\Foundation\Providers\DomainConsoleServiceProvider"']);
        $process->run();

        $process = new Process(['php', $this->laravelAppPath.'/artisan', 'domain:remove', $this->site1, '--force']);
        $process->run();
        $process = new Process(['php', $this->laravelAppPath.'/artisan', 'domain:remove', $this->site2, '--force']);
        $process->run();
        $process = new Process(['php', $this->laravelAppPath.'/artisan', 'domain:remove', $this->subSite1, '--force']);
        $process->run();

        $process = new Process(['php', $this->laravelAppPath.'/artisan', 'domain:add', $this->site1]);
        $process->run();
        $process = new Process(['php', $this->laravelAppPath.'/artisan', 'domain:add', $this->site2]);
        $process->run();
        $process = new Process(['php', $this->laravelAppPath.'/artisan', 'domain:add', $this->subSite1]);
        $process->run();

        $domainValues = [
            'APP_NAME' => $this->siteAppName1,
            'DB_DATABASE' => $this->siteDbName1,
        ];
        $process = new Process(['php', $this->laravelAppPath.'/artisan', 'domain:update_env', $this->site1, '--domain_values='.json_encode($domainValues)]);
        $process->run();

        $domainValues = [
            'APP_NAME' => $this->siteAppName2,
            'DB_DATABASE' => $this->siteDbName2,
        ];
        $process = new Process(['php', $this->laravelAppPath.'/artisan', 'domain:update_env', $this->site2, '--domain_values='.json_encode($domainValues)]);
        $process->run();

        $domainValues = [
            'APP_NAME' => $this->subSiteAppName1,
            'DB_DATABASE' => $this->subSiteDbName1,
        ];
        $process = new Process(['php', $this->laravelAppPath.'/artisan', 'domain:update_env', $this->subSite1, '--domain_values='.json_encode($domainValues)]);
        $process->run();

        parent::setUp();


    }

    protected function tearDown(): void
    {
        $this->artisan('domain:remove', ['domain' => $this->site1, '--force' => 1]);
        $this->artisan('domain:remove', ['domain' => $this->site2, '--force' => 1]);
        $this->artisan('domain:remove', ['domain' => $this->subSite1, '--force' => 1]);
        parent::tearDown(); // TODO: Change the autogenerated stub

    }


    /**
     * Resolve application Console Kernel implementation.
     *
     * @param \Illuminate\Foundation\Application $app
     * @return void
     */
    protected function resolveApplicationConsoleKernel($app)
    {
        $app->singleton('Illuminate\Contracts\Console\Kernel', ConsoleKernel::class);
    }

    protected function resolveApplicationHttpKernel($app)
    {
        $app->singleton('Illuminate\Contracts\Http\Kernel', HttpKernel::class);
    }

    protected function resolveApplication()
    {
        return tap(new Application($this->getBasePath()), function ($app) {
            $app->bind(
                'Illuminate\Foundation\Bootstrap\LoadConfiguration',
                'Orchestra\Testbench\Bootstrap\LoadConfiguration'
            );
        });
    }

    /**
     * Define environment setup.
     *
     * @param \Illuminate\Foundation\Application $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        // set up database configuration
    }

    /**
     * Get Sluggable package providers.
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            TestServiceProvider::class,
            DomainConsoleServiceProvider::class,
//            ServiceProvider::class,
        ];
    }


    /**
     * In this test we simply checks that in the route "/" we display
     * the right APP_NAME from the env file depending upon the $_SERVER['SERVER_NAME'] value
     * set when the test has been launched.
     *
     */
    public function testWelcomePage()
    {

        $this->serverName = Arr::get($_SERVER, 'SERVER_NAME');
        $stringToSee = 'Laravel';
        if (in_array($this->serverName, [$this->subSite1])) {
            $stringToSee = $this->subSiteAppName1;
        } elseif (in_array($this->serverName, [$this->site1]) || Str::endsWith($this->serverName, '.'.$this->site1)) {
            $stringToSee = $this->siteAppName1;
        } elseif (in_array($this->serverName, [$this->site2]) || Str::endsWith($this->serverName, '.'.$this->site2)) {
            $stringToSee = $this->siteAppName2;
        }

        $this->visit('http://' . $this->serverName)
            ->see($stringToSee);
    }


    /**
     * In this test we checks that the database connection is versus the right database set in the
     * DB_DATABASE entry of the env file.
     * The chosen env files again depends upon the $_SERVER['SERVER_NAME'] value
     * set when the test has been launched.
     *
     */
    public function testDBConnection()
    {

        $this->serverName = Arr::get($_SERVER, 'SERVER_NAME');
        $dbName = DB::connection('mysql')->getDatabaseName();
        $expectedDb = 'homestead';
        if (in_array($this->serverName, [$this->subSite1])) {
            $expectedDb = $this->subSiteDbName1;
        } elseif (in_array($this->serverName, [$this->site1]) || Str::endsWith($this->serverName, '.'.$this->site1)) {
            $expectedDb = $this->siteDbName1;
        } elseif (in_array($this->serverName, [$this->site2]) || Str::endsWith($this->serverName, '.'.$this->site2)) {
            $expectedDb = $this->siteDbName2;
        }

        $this->assertEquals($expectedDb, $dbName);
    }


    /**
     * In this test we checks that the database connection is versus the right database set in the
     * DB_DATABASE entry of the env file.
     * The chosen env files again depends upon the $_SERVER['SERVER_NAME'] value
     * set when the test has been launched.
     *
     */
    public function testEnvFile()
    {

        $this->serverName = Arr::get($_SERVER, 'SERVER_NAME');
        $envfileName = app()->environmentFile();
        $expectedEnvFile = '.env';
        if (in_array($this->serverName, [$this->subSite1])) {
            $expectedEnvFile = '.env.'.$this->subSite1;
        } elseif (in_array($this->serverName, [$this->site1]) || Str::endsWith($this->serverName, '.'.$this->site1)) {
            $expectedEnvFile = '.env.'.$this->site1;
        } elseif (in_array($this->serverName, [$this->site2]) || Str::endsWith($this->serverName, '.'.$this->site2)) {
            $expectedEnvFile = '.env.'.$this->site2;
        }

        $this->assertEquals($expectedEnvFile, $envfileName);
    }

    /**
     * In this test we checks that the database connection is versus the right database set in the
     * DB_DATABASE entry of the env file.
     * The chosen env files again depends upon the $_SERVER['SERVER_NAME'] value
     * set when the test has been launched.
     *
     */
    public function testStorageFolder()
    {

        $this->serverName = Arr::get($_SERVER, 'SERVER_NAME');
        $storageFolder = storage_path();
        $expectedStorageFolder = base_path() . '/storage';

        if (in_array($this->serverName, [$this->subSite1])) {
            $expectedStorageFolder = $expectedStorageFolder . DIRECTORY_SEPARATOR . domain_sanitized($this->subSite1);
        } elseif (in_array($this->serverName, [$this->site1]) || Str::endsWith($this->serverName, '.'.$this->site1)) {
            $expectedStorageFolder = $expectedStorageFolder . DIRECTORY_SEPARATOR . domain_sanitized($this->site1);
        } elseif (in_array($this->serverName, [$this->site2]) || Str::endsWith($this->serverName, '.'.$this->site2)) {
            $expectedStorageFolder = $expectedStorageFolder . DIRECTORY_SEPARATOR . domain_sanitized($this->site2);
        }

        $this->assertEquals($expectedStorageFolder, $storageFolder);
    }


}