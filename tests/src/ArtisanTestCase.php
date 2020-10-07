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
use Illuminate\Support\Str;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;

class ArtisanTestCase extends TestCase
{


    protected $files = null;

    protected $laravelAppPath = null;

    protected $laravelEnvPath = null;

    protected $laravelArtisanFile = 'artisan';

    protected $site1 = 'site1.test';
    protected $site2 = 'site2.test';
    protected $subSite1 = 'sub1.site1.test';

    protected $siteAppName1 = 'APPSite1';
    protected $siteAppName2 = 'APPSite2';
    protected $subSiteAppName1 = 'APPSubSite1';

    /*
     * Added for changes in artisan ouput in Laravel 5.7
     */
    public $mockConsoleOutput = false;


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
    protected function setUp(): void
    {
        $this->files = new Filesystem();
        $this->setPaths();
        copy($this->laravelAppPath.'/config/app.php',$this->laravelAppPath.'/config/appORIG.php');
        copy(__DIR__ . '/../config/app.php',$this->laravelAppPath.'/config/app.php');
        copy($this->laravelAppPath.'/config/queue.php',$this->laravelAppPath.'/config/queueORIG.php');
        copy(__DIR__ . '/../config/queue.php',$this->laravelAppPath.'/config/queue.php');
        if (!is_dir($this->laravelEnvPath)) {
            mkdir($this->laravelEnvPath);
        }
        copy(__DIR__ . '/../.env.example',$this->laravelEnvPath.'/.env');
        copy(__DIR__ . '/../'.$this->laravelArtisanFile,$this->laravelAppPath.DIRECTORY_SEPARATOR.'artisan');

        foreach ($this->files->allFiles(__DIR__ . '/../database/migrations') as $file) {
            $relativeFile = substr($file,strrpos($file,'/'));
            copy($file,$this->laravelAppPath.'/database/migrations/'.$relativeFile);
        }

        $process = new Process(['php', $this->laravelAppPath.'/artisan', 'vendor:publish', '--provider="Gecche\Multidomain\Foundation\Providers\DomainConsoleServiceProvider"']);
        $process->run();

        parent::setUp();

    }

    protected function setPaths() {
        $this->laravelAppPath = __DIR__ . '/../../vendor/orchestra/testbench-core/laravel';
        $this->laravelEnvPath = $this->laravelAppPath;
    }

    protected function tearDown(): void {

        $this->files->delete($this->laravelEnvPath.'/.env');
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

        $process = new Process(['php', $this->laravelAppPath.'/artisan', 'domain:add', 'site1.test']);
        $process->run();

        $domainValues = ['APP_NAME'=>'LARAVELTEST'];
        $process = new Process(['php', $this->laravelAppPath.'/artisan', 'domain:update_env', 'site1.test', '--domain_values='.json_encode($domainValues)]);
        $process->run();

        $process = new Process(['php', $this->laravelAppPath.'/artisan', 'name']);
        $process->run();

        if (in_array($serverName, ['site1.test']) || Str::endsWith($serverName,'.site1.test')) {
            $this->assertEquals("LARAVELTEST",$process->getOutput());
        } else {
            $this->assertEquals("Laravel",$process->getOutput());
        }

        $process = new Process(['php', $this->laravelAppPath.'/artisan', 'key:generate', '--domain=site1.test']);
        $process->run();

        $process = new Process(['php', $this->laravelAppPath.'/artisan', 'name', '--domain=site1.test']);
        $process->run();
        $this->assertEquals("LARAVELTEST",$process->getOutput());

        $process = new Process(['php', $this->laravelAppPath.'/artisan', 'domain:remove', 'site1.test', '--force']);
        $process->run();


        return;

    }

    /*
     * TEST FOR key:generate COMMAND
     */
    public function testKeyGenerateCommand() {

        //Note that if the $_SERVER['SERVER_NAME'] value has been set and the --domain option has NOT been set,
        //the $_SERVER['SERVER_NAME'] value acts as the --domain option value.
        $serverName = Arr::get($_SERVER,'SERVER_NAME');

        $process = new Process(['php', $this->laravelAppPath.'/artisan', 'domain:add', 'site1.test']);
        $process->run();

        $domainValues = ['APP_NAME'=>'LARAVELTEST'];
        $process = new Process(['php', $this->laravelAppPath.'/artisan', 'domain:update_env', 'site1.test', '--domain_values='.json_encode($domainValues)]);
        $process->run();

        $process = new Process(['php', $this->laravelAppPath.'/artisan', 'key:generate']);
        $process->run();
        $keyGenerateOutput = substr($process->getOutput(),17);
        $keyGenerateOutput = substr($keyGenerateOutput,0,strrpos($keyGenerateOutput,']'));


        if (in_array($serverName, ['site1.test']) || Str::endsWith($serverName,'.site1.test')) {
            $envFile = '.env.site1.test';
        } else {
            $envFile = '.env';
        }

        $this->assertStringContainsString('APP_KEY='.$keyGenerateOutput,$this->files->get($this->laravelEnvPath.DIRECTORY_SEPARATOR.$envFile));

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
        $process = new Process(['php', $this->laravelAppPath.'/artisan', 'domain:add', 'site1.test']);
        $process->run();

        $domainValues = ['DB_DATABASE'=>'site1'];
        $process = new Process(['php', $this->laravelAppPath.'/artisan', 'domain:update_env', 'site1.test', '--domain_values='.json_encode($domainValues)]);
        $process->run();


        //RESET MIGRATIONS IN BOTH DBS
        $process = new Process(['php', $this->laravelAppPath.'/artisan', 'migrate:reset']);
        $process->run();

        $process = new Process(['php', $this->laravelAppPath.'/artisan', 'migrate:reset', '--domain=site1.test']);
        $process->run();

        //MIGRATIONS WITHOUT DOMAIN OPTION
        $process = new Process(['php', $this->laravelAppPath.'/artisan', 'migrate']);
        $process->run();
//        $this->assertEquals("Laravel",$process->getOutput());


        //CHECK QUEUE:FLUSH COMMAND: SUCCESS WITHOUT OPTIONS AND FAILURE WITH DOMAIN OPTION
        $process = new Process(['php', $this->laravelAppPath.'/artisan', 'queue:flush']);
        $process->run();
        $this->assertStringContainsString('All failed jobs deleted successfully!',$process->getOutput());

        $process = new Process(['php', $this->laravelAppPath.'/artisan', 'queue:flush', '--domain=site1.test']);
        $process->run();
        if (in_array($serverName, ['site1.test']) || Str::endsWith($serverName,'.site1.test')) {
            $this->assertStringContainsString('All failed jobs deleted successfully!',$process->getOutput());
        } else {
            $this->assertStringContainsString('SQLSTATE[42S02]: Base table or view not found: 1146 Table \'site1.failed',$process->getOutput());
        }



        //RESET MIGRATIONS IN BOTH DBS
        $process = new Process(['php', $this->laravelAppPath.'/artisan', 'migrate:reset']);
        $process->run();
        $process = new Process(['php', $this->laravelAppPath.'/artisan', 'migrate:reset', '--domain=site1.test']);
        $process->run();

        //MIGRATIONS WITH DOMAIN OPTION
        $process = new Process(['php', $this->laravelAppPath.'/artisan', 'migrate', '--domain=site1.test']);
        $process->run();
//        $this->assertEquals("Laravel",$process->getOutput());

        //CHECK QUEUE:FLUSH COMMAND: SUCCESS WITH DOMAIN OPTIION AND FAILURE WITHOUT
        $process = new Process(['php', $this->laravelAppPath.'/artisan', 'queue:flush', '--domain=site1.test']);
        $process->run();
        $this->assertStringContainsString('All failed jobs deleted successfully!',$process->getOutput());

        $process = new Process(['php', $this->laravelAppPath.'/artisan', 'queue:flush']);
        $process->run();
        if (in_array($serverName, ['site1.test']) || Str::endsWith($serverName,'.site1.test')) {
            $this->assertStringContainsString('All failed jobs deleted successfully!',$process->getOutput());
        } else {
            $this->assertStringContainsString('SQLSTATE[42S02]: Base table or view not found: 1146 Table \'homestead.failed',$process->getOutput());
        }

        $process = new Process(['php', $this->laravelAppPath.'/artisan', 'domain:remove', 'site1.test', '--force']);
        $process->run();

        return;

    }

    /*
       * TEST FOR QUEUE LISTEN COMMAND
       * In this test we check that queues listeners applied to distinct domains work with the jobs pushed from
       * the corresponding domain and that the queues where they are pushed are those configured in the .env files.
       *
       * The job Gecche\Multidomain\Tests\Jobs\AppNameJob simply put in a file the name of the queue and the env(APP_NAME) value.
       * We push that job using an artisan Gecche\Multidomain\Tests\Console\Commands\QueuePush (queue_push) command which simply push the job in the queue.
       *
       */
    public function testQueueListenCommand() {


        //Note that if the $_SERVER['SERVER_NAME'] value has been set and the --domain option has NOT been set,
        //the $_SERVER['SERVER_NAME'] value acts as the --domain option value.
        // So all the artisan commands run as if the option were instantiated.
        $serverName = Arr::get($_SERVER,'SERVER_NAME');


        //ADDING DOMAIN AND UPDATING ENV
        $process = new Process(['php', $this->laravelAppPath.'/artisan', 'domain:add', 'site1.test']);
        $process->run();

        $domainValues = [
            'APP_NAME' => 'LARAVELTEST',
            'DB_DATABASE'=>'site1',
            'QUEUE_DEFAULT' => 'site1'
        ];
        $process = new Process(['php', $this->laravelAppPath.'/artisan', 'domain:update_env', 'site1.test', '--domain_values='.json_encode($domainValues)]);
        $process->run();


        //RESET MIGRATIONS IN BOTH DBS
        $process = new Process(['php', $this->laravelAppPath.'/artisan', 'migrate:reset']);
        $process->run();
        $process = new Process(['php', $this->laravelAppPath.'/artisan', 'migrate:reset', '--domain=site1.test']);
        $process->run();

        //MIGRATIONS WITHOUT DOMAIN OPTION
        $process = new Process(['php', $this->laravelAppPath.'/artisan', 'migrate']);
        $process->run();


        //This the file used by the AppNameJob job
        $fileToTest = $this->laravelAppPath.'/queueresult.txt';

        $this->files->delete($fileToTest);

        $this->assertFileDoesNotExist($fileToTest);

        //We start the listener
        $processName = "php ".$this->laravelAppPath. "/artisan queue:listen";
        $process = new Process(explode(" ",$processName));
        $process->start();

        //We push the Job using the queue_push command
        $process2 = new Process(['php', $this->laravelAppPath.'/artisan', 'queue_push']);
        $process2->run();

        //Wait for a reasonable time (the only way we managed to simulate queue listeners)
        sleep(5);

        //We assert that the file exists
        $this->assertFileExists($fileToTest);

        //Depending upon the domain option (or the SERVER_NAME value)
        //we check accordingly the contents of the file
        $fileContent = $this->files->get($fileToTest);
        if (in_array($serverName, ['site1.test']) || Str::endsWith($serverName,'.site1.test')) {
            $this->assertStringContainsString('LARAVELTEST --- site1',$fileContent);
        } else {
            $this->assertStringContainsString('Laravel --- default',$fileContent);
        }


        $process->stop(0);

        //We kill the listener process by name
        $string = 'pkill -f "' . $processName . '"';
        exec($string);


        /*
         * We repeat the stuff under the domain "site1.test"
         */
        /***/
        //MIGRATIONS WITH DOMAIN OPTION
        $process = new Process(['php', $this->laravelAppPath.'/artisan', 'migrate', '--domain=site1.test']);
        $process->run();

        $this->files->delete($fileToTest);

        $this->assertFileDoesNotExist($fileToTest);

        $processName = 'php '.$this->laravelAppPath.'/artisan queue:listen --domain=site1.test';
        $process = new Process(explode(" ",$processName));
        $process->start();

        $process2 = new Process(['php', $this->laravelAppPath.'/artisan', 'queue_push', '--domain=site1.test']);
        $process2->run();

        //Wait for a reasonable time
        sleep(5);

        $this->assertFileExists($fileToTest);

        $fileContent = $this->files->get($fileToTest);
        $this->assertStringContainsString('LARAVELTEST --- site1',$fileContent);

        $process->stop(0);

        $string = 'pkill -f "' . $processName . '"';
        exec($string);

        $process = new Process(['php', $this->laravelAppPath.'/artisan', 'domain:remove', 'site1.test', '--force']);
        $process->run();


        $this->files->delete($fileToTest);

        return;

    }
}
