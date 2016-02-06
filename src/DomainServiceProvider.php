<?php

namespace Gecche\Multidomain;

use App;
use Illuminate\Support\ServiceProvider;

class DomainServiceProvider extends ServiceProvider {

    protected $defer = false;

    public function register() {


        App::bind("domain", function($app) {
            return new Domain(
                    $app['files']
            );
        });

    }

    public function boot() {

    }

    public function provides() {
        return [
            "domain",
        ];
    }

}
