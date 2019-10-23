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
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Gecche\Multidomain\Tests\Console\Kernel as ConsoleKernel;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;

class CommandsTestCase extends TestCase
{


    protected $files = null;

    protected $laravelAppPath = null;

    protected $site = 'site1.test';
    protected $siteDbName = 'db_site1';

    /**
     * Setup the test environment.
     *
     * Tests need an .env file and the domain.php config file published.
     * This is what we do in the setUp method
     *
     *
     * @return void
     */
    public function setUp()
    {
        $this->laravelAppPath = __DIR__ . '/../../vendor/orchestra/testbench-core/laravel';
        copy(__DIR__ . '/../.env.example',$this->laravelAppPath.'/.env');
        copy(__DIR__ . '/../artisan',$this->laravelAppPath.'/artisan');

        parent::setUp();

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


    /*
     * TEST FOR DOMAIN LIST COMMAND
     * First we add the domain <SITE> adn we check <SITE> is in the output of the domain:list command.
     * Then we remove the domain <SITE> adn we check <SITE> is no more in the output of the domain:list command.
     */
    public function testDomainListCommand() {


        $process = new Process('php '.$this->laravelAppPath.'/artisan domain:add site1.test');
        $process->run();

        $process = new Process('php '.$this->laravelAppPath.'/artisan domain:update_env site1.test --domain_values=\'{"APP_NAME":"LARAVELTEST"}\'');
        $process->run();

        $process = new Process('php '.$this->laravelAppPath.'/artisan name');
        $process->run();
        $this->assertEquals("Laravel",$process->getOutput());

        $process = new Process('php '.$this->laravelAppPath.'/artisan name --domain=site1.test');
        $process->run();
        $this->assertEquals("LARAVELTEST",$process->getOutput());
        return;

    }
}