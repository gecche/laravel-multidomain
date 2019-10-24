<?php
/**
 * Created by PhpStorm.
 * User: gecche
 * Date: 01/10/2019
 * Time: 11:15
 */

namespace Gecche\Multidomain\Tests;

use Illuminate\Filesystem\Filesystem;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;

class ArtisanTestCase extends TestCase
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
        $this->files = new Filesystem();
        $this->laravelAppPath = __DIR__ . '/../../vendor/orchestra/testbench-core/laravel';
        copy(__DIR__ . '/../.env.example',$this->laravelAppPath.'/.env');
        copy(__DIR__ . '/../artisan',$this->laravelAppPath.'/artisan');

        foreach ($this->files->allFiles(__DIR__ . '/../database/migrations') as $file) {
            $relativeFile = substr($file,strrpos($file,'/'));
            copy($file,$this->laravelAppPath.'/database/migrations/'.$relativeFile);
        }

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
     * TEST FOR A BASIC COMMAND
     * We created a very simple "name" command (Gecche\Multidomain\Tests\Console\Commands\NameCommand) which simply displays the
     * text in the APP_NAME environment variable.
     * The default .env file containts APP_NAME=Laravel
     * First we create a new domain site1.test and we update his .env with APP_NAME=LARAVELTEST
     * Then we launch the "name" command twice: without options and with the --domain=site1.test option.
     * We expect to see "Laravel" andd then "LARAVELTEST" accordingly.
     */
    public function testBasicCommand() {


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

    /*
    * TEST FOR QUEUE COMMANDS
    * In this test we check that queues with the database driver refer to the correct db depending upon the domain.
    * We use for simplicity the queue:flush command.
    * The default .env file containts QUEUE_DRIVER=database and DB_DATABASE=homestead
    * First we create a new domain site1.test and we update his .env with DB_DATABASE=site1 and we reset and run the migrations
    * in the homestead db.
    * Then, we check that the queue:flush command launched without options displays the standard success message
    * "All failed jobs deleted successfully!" and that the queue:flush command launched with the --domain=site1.test
    * option fails because it doesn't find the failed_job table as we did'nt migrate for that db.
    * Last, we repeat the check with the other db.
    *
    */
    public function testQueueCommand() {


        //ADDING DOMAIN AND UPDATING ENV
        $process = new Process('php '.$this->laravelAppPath.'/artisan domain:add site1.test');
        $process->run();

        $process = new Process('php '.$this->laravelAppPath.'/artisan domain:update_env site1.test --domain_values=\'{"DB_DATABASE":"site1"}\'');
        $process->run();


        //RESET MIGRATIONS IN BOTH DBS
        $process = new Process('php '.$this->laravelAppPath.'/artisan migrate:reset');
        $process->run();
        $process = new Process('php '.$this->laravelAppPath.'/artisan migrate:reset --domain=site1.test');
        $process->run();

        //MIGRATIONS WITHOUT DOMAIN OPTION
        $process = new Process('php '.$this->laravelAppPath.'/artisan migrate');
        $process->run();
//        $this->assertEquals("Laravel",$process->getOutput());


        //CHECK QUEUE:FLUSH COMMAND: SUCCESS WITHOUT OPTIONS AND FAILURE WITH DOMAIN OPTION
        $process = new Process('php '.$this->laravelAppPath.'/artisan queue:flush');
        $process->run();
        $this->assertContains('All failed jobs deleted successfully!',$process->getOutput());

        $process = new Process('php '.$this->laravelAppPath.'/artisan queue:flush --domain=site1.test');
        $process->run();
        $this->assertContains('SQLSTATE[42S02]: Base table or view not found: 1146 Table \'site1.failed',$process->getOutput());



        //RESET MIGRATIONS IN BOTH DBS
        $process = new Process('php '.$this->laravelAppPath.'/artisan migrate:reset');
        $process->run();
        $process = new Process('php '.$this->laravelAppPath.'/artisan migrate:reset --domain=site1.test');
        $process->run();

        //MIGRATIONS WITH DOMAIN OPTION
        $process = new Process('php '.$this->laravelAppPath.'/artisan migrate --domain=site1.test');
        $process->run();
//        $this->assertEquals("Laravel",$process->getOutput());

        //CHECK QUEUE:FLUSH COMMAND: SUCCESS WITH DOMAIN OPTIION AND FAILURE WITHOUT
        $process = new Process('php '.$this->laravelAppPath.'/artisan queue:flush --domain=site1.test');
        $process->run();
        $this->assertContains('All failed jobs deleted successfully!',$process->getOutput());

        $process = new Process('php '.$this->laravelAppPath.'/artisan queue:flush');
        $process->run();
        $this->assertContains('SQLSTATE[42S02]: Base table or view not found: 1146 Table \'homestead.failed',$process->getOutput());

        return;

    }
}