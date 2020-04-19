<?php
/**
 * Created by PhpStorm.
 * User: gecche
 * Date: 01/10/2019
 * Time: 11:15
 */

namespace Gecche\Multidomain\Tests;

use Gecche\Multidomain\Foundation\Application;

class CommandsSubfolderTestCase extends CommandsTestCase
{

    protected $envPath = 'envs';

    protected $files = null;

    protected $site = 'site1.test';
    protected $siteDbName = 'db_site1';



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