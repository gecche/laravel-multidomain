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

class ArtisanSubfolderTestCase extends ArtisanTestCase
{


    protected $files = null;

    protected $laravelAppPath = null;

    protected $laravelEnvPath = null;

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
        $this->laravelEnvPath = $this->laravelAppPath . DIRECTORY_SEPARATOR . 'envs';
        copy($this->laravelAppPath.'/config/app.php',$this->laravelAppPath.'/config/appORIG.php');
        copy(__DIR__ . '/../config/app.php',$this->laravelAppPath.'/config/app.php');
        copy($this->laravelAppPath.'/config/queue.php',$this->laravelAppPath.'/config/queueORIG.php');
        copy(__DIR__ . '/../config/queue.php',$this->laravelAppPath.'/config/queue.php');
        copy(__DIR__ . '/../.env.example',$this->laravelEnvPath.'/.env');
        copy(__DIR__ . '/../artisan_sub',$this->laravelAppPath.'/artisan');

        foreach ($this->files->allFiles(__DIR__ . '/../database/migrations') as $file) {
            $relativeFile = substr($file,strrpos($file,'/'));
            copy($file,$this->laravelAppPath.'/database/migrations/'.$relativeFile);
        }

        $process = new Process('php '.$this->laravelAppPath.'/artisan vendor:publish --provider="Gecche\Multidomain\Foundation\Providers\DomainConsoleServiceProvider"');
        $process->run();

        parent::setUp();

    }


    protected function tearDown() {

        $this->files->delete($this->laravelEnvPath.'/.env');
        copy($this->laravelAppPath.'/config/appORIG.php',$this->laravelAppPath.'/config/app.php');
        $this->files->delete($this->laravelAppPath.'/config/appORIG.php');
        copy($this->laravelAppPath.'/config/queueORIG.php',$this->laravelAppPath.'/config/queue.php');
        $this->files->delete($this->laravelAppPath.'/config/queueORIG.php');

    }



}