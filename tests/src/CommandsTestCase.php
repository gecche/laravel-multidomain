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

class CommandsTestCase extends \Orchestra\Testbench\TestCase
{


    protected $files = null;

    protected $siteDbName = 'db_site1';

    protected $laravelAppPath;
    protected $laravelEnvPath;

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

        $this->setPaths();

        $this->files = new Filesystem();

        if (!is_dir(env_path())) {
            mkdir(env_path());
        }

        copy(__DIR__ . '/../.env.example',env_path('.env'));

        $this->artisan('vendor:publish',['--provider' => 'Gecche\Multidomain\Foundation\Providers\DomainConsoleServiceProvider']);


    }

    protected function setPaths()
    {
        $this->laravelAppPath = __DIR__ . '/../../vendor/orchestra/testbench-core/laravel';
        $this->laravelEnvPath = $this->laravelAppPath;
    }

    /**
     * Resolve application Console Kernel implementation.
     *
     * @param  \Illuminate\Foundation\Application  $app
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
     * @param  \Illuminate\Foundation\Application $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {

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
        ];
    }


    /*
         * TEST FOR DOMAIN COMMAND
         * First we add the domain <SITE> adn we check <SITE> is in the output of the domain:list command.
         * Then we remove the domain <SITE> adn we check <SITE> is no more in the output of the domain:list command.
         */
    public function testDomainCommand() {


        $serverName = Arr::get($_SERVER,'SERVER_NAME','');

        $this->artisan('domain');

        $artisanOutput = Artisan::output();

        //CHECK <SITE> IS IN THE OUTPUT OF THE COMMAND
        $this->assertStringContainsString($serverName,$artisanOutput);


    }

    /*
     * TEST FOR DOMAIN ADD COMMAND
     * It checks if the env file and storage dirs exist and if the list of domains in the config file is updated
     */
    public function testDomainAddCommand()
    {
        $site = Arr::get($_SERVER, 'SERVER_NAME');

        if (!$site) {
            $this->assertTrue(true);
            return;
        }

        $argDomain = $site ? ['domain' => $site] : [];

        $this->artisan('domain:add', $argDomain);

        $this->assertFileExists(env_path('.env.'.$site));

        $this->artisan('config:clear');

        $domainListed = Config::get('domain.domains');

        $this->assertArrayHasKey($site,$domainListed);

        $this->assertDirectoryExists(app()->exactDomainStoragePath());
    }

    /*
     * TEST FOR DOMAIN REMOVE COMMAND
     * It checks if the .env file does not exist and if the list of domains in the config file is updated without the domain.
     * It checks also if storage dirs still exist
     */
    public function testDomainRemoveCommand()
    {
        $site = Arr::get($_SERVER, 'SERVER_NAME');
        if (!$site) {
            $this->assertTrue(true);
            return;
        }
        $argDomain = $site ? ['domain' => $site] : [];

        $this->artisan('domain:remove', $argDomain);

        $this->assertFileDoesNotExist(env_path('.env.'.$site));

        $domainListed = Config::get('domain.domains');

        $this->assertArrayNotHasKey($site,$domainListed);

        $this->assertDirectoryExists(app()->exactDomainStoragePath());
    }

    /*
     * TEST FOR DOMAIN REMOVE COMMAND (FORCE)
     * It checks if the .env file does not exist and if the list of domains in the config file is updated without the domain.
     * Now it checks also if storage dirs does not exist (force)
     */
    public function testDomainRemoveForceCommand()
    {
        $site = Arr::get($_SERVER, 'SERVER_NAME');
        if (!$site) {
            $this->assertTrue(true);
            return;
        }
        $argDomain = $site ? ['domain' => $site, '--force' => 1] : ['--force' => 1];

        $this->artisan('domain:remove', $argDomain);

        $this->assertFileDoesNotExist(env_path('.env.'.$site));

        $domainListed = Config::get('domain.domains');

        $this->assertArrayNotHasKey($site,$domainListed);

        //$this->assertDirectoryDoesNotExist(storage_path(domain_sanitized($site)));
        $this->assertDirectoryDoesNotExist(app()->exactDomainStoragePath());
    }

    /*
     * TEST FOR DOMAIN REMOVE COMMAND (FORCE)
     * It checks if the .env file does not exist and if the list of domains in the config file is updated without the domain.
     * Now it checks also if storage dirs does not exist (force)
     */
    public function testDomainRemoveForceCommandSubsite()
    {

        $mainStoragePath = $this->laravelAppPath . DIRECTORY_SEPARATOR . 'storage';
        $sites = [
            'site1.com',
            'sub1.site1.com',
            'sub2.site1.com',
        ];
        foreach ($sites as $currSite) {
            $this->artisan('domain:add', ['domain' => $currSite]);
            $this->assertFileExists(env_path('.env.'.$currSite));
            $this->assertDirectoryExists($mainStoragePath . DIRECTORY_SEPARATOR . domain_sanitized($currSite));
        }


        $site = 'sub1.site1.com';
        $argDomain = ['domain' => $site, '--force' => 1];
        $this->artisan('domain:remove', $argDomain);

        foreach ($sites as $currSite) {
            if ($site == $currSite) {
                $this->assertFileDoesNotExist(env_path('.env.'.$currSite));
                $this->assertDirectoryDoesNotExist($mainStoragePath . DIRECTORY_SEPARATOR . domain_sanitized($currSite));
            } else {
                $this->assertFileExists(env_path('.env.'.$currSite));
                $this->assertDirectoryExists($mainStoragePath . DIRECTORY_SEPARATOR . domain_sanitized($currSite));
            }
        }

        foreach ($sites as $currSite) {
            if ($site != $currSite) {
                $this->artisan('domain:remove', ['domain' => $currSite,'--force' => 1]);
                $this->assertFileDoesNotExist(env_path('.env.'.$currSite));
                $this->assertDirectoryDoesNotExist($mainStoragePath . DIRECTORY_SEPARATOR . domain_sanitized($currSite));
            }
        }

    }

    /*
     * TEST FOR DOMAIN UPDATE_ENV COMMAND
     * First we remove and add the domain <SITE> to be sure <SITE> is fresh.
     * Then we check the DB_DATABASE value in the corresponding .env.<SITE>.
     * We update  the DB_DATABASE value and then we check the final value.
     */
    public function testDomainUpdateEnvCommand() {

        $dbName = $this->siteDbName;
        $site = Arr::get($_SERVER, 'SERVER_NAME');
        if (!$site) {
            $this->assertTrue(true);
            return;
        }
        $argDomain = $site ? ['domain' => $site] : [];

        $this->artisan('domain:remove', array_merge($argDomain, ['--force' => 1]));
        $this->artisan('domain:add', $argDomain);

        $fileContents = explode("\n",$this->files->get(env_path('.env.'.$site)));
        $this->assertNotContains("DB_DATABASE=".$dbName,$fileContents);

        $this->artisan('domain:update_env', array_merge($argDomain, [
            '--domain_values' => '{"DB_DATABASE":"'.$dbName.'"}',
        ]));

        $fileContents = explode("\n",$this->files->get(env_path('.env.'.$site)));
        $this->assertContains("DB_DATABASE=".$dbName,$fileContents);

        $this->artisan('domain:remove', array_merge($argDomain, ['--force' => 1]));

    }

    /*
     * TEST FOR DOMAIN LIST COMMAND
     * First we add the domain <SITE> adn we check <SITE> is in the output of the domain:list command.
     * Then we remove the domain <SITE> adn we check <SITE> is no more in the output of the domain:list command.
     */
    public function testDomainListCommand() {

        $site = Arr::get($_SERVER, 'SERVER_NAME');
        $argDomain = $site ? ['domain' => $site] : [];

        //ADD THE DOMAIN <SITE>
        if ($site) {
            $this->artisan('domain:add', $argDomain);
        }

        $this->artisan('domain:list');

        $artisanOutput = Artisan::output();

        //CHECK <SITE> IS IN THE OUTPUT OF THE COMMAND
        if ($site) {
        $this->assertStringContainsString($site,$artisanOutput);
        } else {
            $this->assertEquals($site, $artisanOutput);
        }


        //REMOVE THE DOMAIN <SITE>
        if ($site) {
            $this->artisan('domain:remove', array_merge($argDomain, ['--force' => 1]));
        }
        $this->artisan('domain:list');

        $artisanOutput = Artisan::output();

        //CHECK <SITE> IS NOT IN THE OUTPUT OF THE COMMAND
        if ($site) {
        $this->assertStringNotContainsString($site,$artisanOutput);
        } else {
            $this->assertEquals($site, $artisanOutput);
    }
    }


}