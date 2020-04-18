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

    protected $laravelArtisanFile = 'artisan_sub';

    protected function setPaths() {
        $this->laravelAppPath = __DIR__ . '/../../vendor/orchestra/testbench-core/laravel';
        $this->laravelEnvPath = $this->laravelAppPath . DIRECTORY_SEPARATOR . 'envs';
    }

}