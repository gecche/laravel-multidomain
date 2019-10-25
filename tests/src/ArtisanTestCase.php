<?php
/**
 * Created by PhpStorm.
 * User: gecche
 * Date: 01/10/2019
 * Time: 11:15
 */

namespace Gecche\Multidomain\Tests;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;

class ArtisanTestCase extends TestCase
{


    protected $files = null;

    protected $laravelAppPath = null;


    /**
     * Setup the test environment.
     *
     * In these tests we simulate the --domain option applied to two commands.
     *
     * Tests preparation in this is a bit more complex as usual because the --domain option
     * doesn't have any effect if used from within a Laravel app.
     * Please note that this is not an incorrect behaviour and it is consistent with the --env option
     * which again has no effect if used from within a Laravel app.
     * Indeed this TestCase does not extend the Orchestra TestCase but directly the PHPUnit TestCase in order
     * to avoid the environment setup of a Laravel App.
     *
     * So, we need a simulation of the artisan script placed in the base_path directory
     * of the Orchestra Testbench Laravel app and then run the test using the Symfony Process directly
     * and not from within a Laravel app.
     *
     * This is what we do in the setup: we created a copy of the artisan script instantiating the Application and
     * the Providers provided from our package.
     *
     * Tests also need an .env file, the domain.php config file published and some migrations files.
     *
     *
     * @return void
     */
    public function setUp()
    {
        $this->files = new Filesystem();
        $this->laravelAppPath = __DIR__ . '/../../vendor/orchestra/testbench-core/laravel';
        copy($this->laravelAppPath.'/config/app.php',$this->laravelAppPath.'/config/appORIG.php');
        copy(__DIR__ . '/../config/app.php',$this->laravelAppPath.'/config/app.php');
        copy($this->laravelAppPath.'/config/queue.php',$this->laravelAppPath.'/config/queueORIG.php');
        copy(__DIR__ . '/../config/queue.php',$this->laravelAppPath.'/config/queue.php');
        copy(__DIR__ . '/../.env.example',$this->laravelAppPath.'/.env');
        copy(__DIR__ . '/../artisan',$this->laravelAppPath.'/artisan');

        foreach ($this->files->allFiles(__DIR__ . '/../database/migrations') as $file) {
            $relativeFile = substr($file,strrpos($file,'/'));
            copy($file,$this->laravelAppPath.'/database/migrations/'.$relativeFile);
        }

        $process = new Process('php '.$this->laravelAppPath.'/artisan vendor:publish --provider="Gecche\Multidomain\Foundation\Providers\DomainConsoleServiceProvider"');
        $process->run();

        parent::setUp();

    }


    protected function tearDown() {

        $this->files->delete($this->laravelAppPath.'/.env');
        copy($this->laravelAppPath.'/config/appORIG.php',$this->laravelAppPath.'/config/app.php');
        $this->files->delete($this->laravelAppPath.'/config/appORIG.php');
        copy($this->laravelAppPath.'/config/queueORIG.php',$this->laravelAppPath.'/config/queue.php');
        $this->files->delete($this->laravelAppPath.'/config/queueORIG.php');

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

        //Note that if the $_SERVER['SERVER_NAME'] value has been set and the --domain option has NOT been set,
        //the $_SERVER['SERVER_NAME'] value acts as the --domain option value.
        $serverName = Arr::get($_SERVER,'SERVER_NAME');

        $process = new Process('php '.$this->laravelAppPath.'/artisan domain:add site1.test');
        $process->run();

        $process = new Process('php '.$this->laravelAppPath.'/artisan domain:update_env site1.test --domain_values=\'{"APP_NAME":"LARAVELTEST"}\'');
        $process->run();

        $process = new Process('php '.$this->laravelAppPath.'/artisan name');
        $process->run();

        if ($serverName == 'site1.test') {
            $this->assertEquals("LARAVELTEST",$process->getOutput());
        } else {
            $this->assertEquals("Laravel",$process->getOutput());
        }

        $process = new Process('php '.$this->laravelAppPath.'/artisan name --domain=site1.test');
        $process->run();
        $this->assertEquals("LARAVELTEST",$process->getOutput());

        $process = new Process('php '.$this->laravelAppPath.'/artisan domain:remove site1.test --force');
        $process->run();


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
        //Note that if the $_SERVER['SERVER_NAME'] value has been set and the --domain option has NOT been set,
        //the $_SERVER['SERVER_NAME'] value acts as the --domain option value.
        // So all the artisan commands run as if the option were instantiated.
        $serverName = Arr::get($_SERVER,'SERVER_NAME');


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
        if ($serverName == 'site1.test') {
            $this->assertContains('All failed jobs deleted successfully!',$process->getOutput());
        } else {
            $this->assertContains('SQLSTATE[42S02]: Base table or view not found: 1146 Table \'site1.failed',$process->getOutput());
        }



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
        if ($serverName == 'site1.test') {
            $this->assertContains('All failed jobs deleted successfully!',$process->getOutput());
        } else {
            $this->assertContains('SQLSTATE[42S02]: Base table or view not found: 1146 Table \'homestead.failed',$process->getOutput());
        }

        $process = new Process('php '.$this->laravelAppPath.'/artisan domain:remove site1.test --force');
        $process->run();

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
    public function testQueueListenCommand() {


        //Note that if the $_SERVER['SERVER_NAME'] value has been set and the --domain option has NOT been set,
        //the $_SERVER['SERVER_NAME'] value acts as the --domain option value.
        // So all the artisan commands run as if the option were instantiated.
        $serverName = Arr::get($_SERVER,'SERVER_NAME');


        //ADDING DOMAIN AND UPDATING ENV
        $process = new Process('php '.$this->laravelAppPath.'/artisan domain:add site1.test');
        $process->run();

        $process = new Process('php '.$this->laravelAppPath.'/artisan domain:update_env site1.test --domain_values=\'{"APP_NAME":"LARAVELTEST","DB_DATABASE":"site1","QUEUE_DEFAULT":"site1"}\'');
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


        $fileToTest = $this->laravelAppPath.'/queueresult.txt';

        $this->files->delete($fileToTest);

        $this->assertFileNotExists($fileToTest);

        //CHECK QUEUE:FLUSH COMMAND: SUCCESS WITHOUT OPTIONS AND FAILURE WITH DOMAIN OPTION
        $processName = "php ".$this->laravelAppPath. "/artisan queue:listen";
        $process = new Process($processName);
        $process->start();

        $process2 = new Process('php '.$this->laravelAppPath.'/artisan queue_push');
        $process2->run();

        //Wait for a reasonable time
        //echo "Sleep ".$process->getPid()."\n";
        sleep(5);
        //echo "End sleep\n";

        $this->assertFileExists($fileToTest);

        $fileContent = $this->files->get($fileToTest);
        if ($serverName == 'site1.test') {
            $this->assertContains('LARAVELTEST --- site1',$fileContent);
        } else {
            $this->assertContains('Laravel --- default',$fileContent);
        }


        $process->stop(0);

        $string = 'pkill -f "' . $processName . '"';
        //echo $string . "\n";
        exec($string);

        /***/
        //MIGRATIONS WITHOUT DOMAIN OPTION
        $process = new Process('php '.$this->laravelAppPath.'/artisan migrate --domain=site1.test');
        $process->run();

        $this->files->delete($fileToTest);

        $this->assertFileNotExists($fileToTest);

        //CHECK QUEUE:FLUSH COMMAND: SUCCESS WITHOUT OPTIONS AND FAILURE WITH DOMAIN OPTION
        $processName = 'php '.$this->laravelAppPath.'/artisan queue:listen --domain=site1.test';
        $process = new Process($processName);
        $process->start();

        $process2 = new Process('php '.$this->laravelAppPath.'/artisan queue_push --domain=site1.test');
        $process2->run();

        //Wait for a reasonable time
        //echo "Sleep ".$process->getPid()."\n";
        sleep(5);
        //echo "End sleep\n";

        $this->assertFileExists($fileToTest);

        $fileContent = $this->files->get($fileToTest);
        $this->assertContains('LARAVELTEST --- site1',$fileContent);

        $process->stop(0);

        $string = 'pkill -f "' . $processName . '"';
//        echo $string . "\n";
        exec($string);

        $process = new Process('php '.$this->laravelAppPath.'/artisan domain:remove site1.test --force');
        $process->run();


        $this->files->delete($fileToTest);

        return;

    }
}