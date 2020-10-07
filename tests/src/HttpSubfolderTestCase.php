<?php
/**
 * Created by PhpStorm.
 * User: gecche
 * Date: 01/10/2019
 * Time: 11:15
 */

namespace Gecche\Multidomain\Tests;

use Gecche\Multidomain\Foundation\Application;
use Illuminate\Filesystem\Filesystem;
use Orchestra\Testbench\BrowserKit\TestCase;
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

class HttpSubfolderTestCase extends HttpTestCase
{

    protected $serverName;
    protected $laravelAppPath = null;

    protected $envPath = 'envs';

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
        copy(__DIR__ . '/../artisan_sub',$this->laravelAppPath.'/artisan_sub');
        $process = new Process(['php', $this->laravelAppPath.'/artisan_sub', 'config:clear']);
        $process->run();


        $this->files = new Filesystem();
        copy($this->laravelAppPath . '/config/app.php', $this->laravelAppPath . '/config/appORIG.php');
        copy(__DIR__ . '/../config/app.php', $this->laravelAppPath . '/config/app.php');
        if (!is_dir($this->laravelAppPath.'/'.$this->envPath)) {
            mkdir($this->laravelAppPath.'/'.$this->envPath);
        }
        copy(__DIR__ . '/../.env.example', $this->laravelAppPath.'/'.$this->envPath.'/.env');

        $process = new Process(['php', $this->laravelAppPath.'/artisan_sub', 'vendor:publish', '--provider="Gecche\Multidomain\Foundation\Providers\DomainConsoleServiceProvider"']);
        $process->run();

        $process = new Process(['php', $this->laravelAppPath.'/artisan_sub', 'domain:remove', $this->site1, '--force']);
        $process->run();
        $process = new Process(['php', $this->laravelAppPath.'/artisan_sub', 'domain:remove', $this->site2, '--force']);
        $process->run();
        $process = new Process(['php', $this->laravelAppPath.'/artisan_sub', 'domain:remove', $this->subSite1, '--force']);
        $process->run();

        $process = new Process(['php', $this->laravelAppPath.'/artisan_sub', 'domain:add', $this->site1]);
        $process->run();
        $process = new Process(['php', $this->laravelAppPath.'/artisan_sub', 'domain:add', $this->site2]);
        $process->run();
        $process = new Process(['php', $this->laravelAppPath.'/artisan_sub', 'domain:add', $this->subSite1]);
        $process->run();

        $domainValues = [
            'APP_NAME' => $this->siteAppName1,
            'DB_DATABASE' => $this->siteDbName1,
        ];
        $process = new Process(['php', $this->laravelAppPath.'/artisan_sub', 'domain:update_env', $this->site1, '--domain_values='.json_encode($domainValues)]);
        $process->run();

        $domainValues = [
            'APP_NAME' => $this->siteAppName2,
            'DB_DATABASE' => $this->siteDbName2,
        ];
        $process = new Process(['php', $this->laravelAppPath.'/artisan_sub', 'domain:update_env', $this->site2, '--domain_values='.json_encode($domainValues)]);
        $process->run();

        $domainValues = [
            'APP_NAME' => $this->subSiteAppName1,
            'DB_DATABASE' => $this->subSiteDbName1,
        ];
        $process = new Process(['php', $this->laravelAppPath.'/artisan_sub', 'domain:update_env', $this->subSite1, '--domain_values='.json_encode($domainValues)]);
        $process->run();

        TestCase::setUp();


    }


    protected function resolveApplication()
    {
        return tap(new Application($this->getBasePath(), $this->getBasePath().'/'.$this->envPath), function ($app) {
            $app->bind(
                'Illuminate\Foundation\Bootstrap\LoadConfiguration',
                'Orchestra\Testbench\Bootstrap\LoadConfiguration'
            );
        });
    }


}
