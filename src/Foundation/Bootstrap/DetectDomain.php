<?php namespace Gecche\Multidomain\Foundation\Bootstrap;

use Illuminate\Contracts\Foundation\Application;

class DetectDomain {

	/**
	 * Bootstrap the given application.
	 *
	 * @param  \Illuminate\Contracts\Foundation\Application  $app
	 * @return void
	 */
	public function bootstrap(Application $app)
	{

        //Detect the domain
		$app->detectDomain();

        //Overrides the storage path if the domain stoarge path exists
        //$app->useStoragePath($app->domainStoragePath());

	}

}
