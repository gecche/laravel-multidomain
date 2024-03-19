<?php

use Illuminate\Support\Env;
use Orchestra\Testbench\Foundation\Application;
use Orchestra\Testbench\Foundation\Config;
use Orchestra\Testbench\Workbench\Workbench;

use function Illuminate\Filesystem\join_paths;

/**
 * Create Laravel application.
 *
 * @param  string  $workingPath
 * @return \Illuminate\Foundation\Application
 */
$createApp = static function (string $workingPath) {
    $config = Config::loadFromYaml(
        defined('TESTBENCH_WORKING_PATH') ? TESTBENCH_WORKING_PATH : $workingPath
    );

    $hasEnvironmentFile = ! is_null($config['laravel'])
        ? file_exists(join_paths($config['laravel'], '.env'))
        : file_exists(join_paths($workingPath, '.env'));

    return Application::create(
        basePath: $config['laravel'],
        options: ['load_environment_variables' => $hasEnvironmentFile, 'extra' => $config->getExtraAttributes()],
        resolvingCallback: static function ($app) use ($config) {
            Workbench::startWithProviders($app, $config);
            Workbench::discoverRoutes($app, $config);
        },
    );
};

if (! defined('TESTBENCH_WORKING_PATH') && ! is_null(Env::get('TESTBENCH_WORKING_PATH'))) {
    define('TESTBENCH_WORKING_PATH', Env::get('TESTBENCH_WORKING_PATH'));
}

$app = $createApp(realpath(join_paths(__DIR__, '..')));

unset($createApp);

/** @var \Illuminate\Routing\Router $router */
$router = $app->make('router');

collect(glob(join_paths(__DIR__, '..', 'routes', 'testbench-*.php')))
    ->each(static function ($routeFile) use ($app, $router) {
        require $routeFile;
    });

return $app;
