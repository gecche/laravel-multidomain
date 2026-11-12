<?php

namespace Gecche\Multidomain\Tests;

use Gecche\Multidomain\Tests\App\Console\Kernel as ConsoleKernel;
use Gecche\Multidomain\Tests\App\Http\Kernel as HttpKernel;
use Gecche\Multidomain\Foundation\Application;
use Gecche\Multidomain\Foundation\Configuration\ApplicationBuilder;
use Gecche\Multidomain\Foundation\Providers\DomainConsoleServiceProvider;
use Gecche\Multidomain\Tests\App\TestServiceProvider;
use Illuminate\Contracts\Console\Kernel as ConsoleKernelContract;
use Illuminate\Contracts\Http\Kernel as HttpKernelContract;
use Illuminate\Filesystem\Filesystem;
use Orchestra\Testbench\TestCase;

class DomainConfigurationTest extends TestCase
{
    protected $files;

    protected $site = 'site1.test';

    protected $domainConfigMarker = 'DomainConfigMarker';

    protected $laravelAppPath;

    protected $laravelEnvPath;

    public $mockConsoleOutput = false;

    protected function setUp(): void
    {
        parent::setUp();

        $this->setPaths();
        $this->files = new Filesystem();

        if (! is_dir(env_path())) {
            mkdir(env_path());
        }

        copy(__DIR__ . '/../.env.example', env_path('.env'));

        $this->artisan('vendor:publish', [
            '--provider' => 'Gecche\Multidomain\Foundation\Providers\DomainConsoleServiceProvider',
        ]);
    }

    protected function tearDown(): void
    {
        $this->removeDomainConfig($this->site);
        $this->removeCachedConfig($this->site);

        $envFile = env_path('.env.'.$this->site);
        if ($this->files->exists($envFile)) {
            $this->files->delete($envFile);
        }

        $storagePath = base_path('storage' . DIRECTORY_SEPARATOR . domain_sanitized($this->site));
        if ($this->files->isDirectory($storagePath)) {
            $this->files->deleteDirectory($storagePath);
        }

        $configFile = base_path('config/domain.php');
        if ($this->files->exists($configFile)) {
            $config = include $configFile;
            unset($config['domains'][$this->site]);
            $this->files->put($configFile, '<?php return '.var_export($config, true).";\n");
        }

        parent::tearDown();
    }

    protected function setPaths()
    {
        $this->laravelAppPath = __DIR__ . '/../../vendor/orchestra/testbench-core/laravel';
        $this->laravelEnvPath = $this->laravelAppPath;
    }

    protected function resolveApplicationConsoleKernel($app)
    {
        $app->singleton(ConsoleKernelContract::class, ConsoleKernel::class);
    }

    protected function resolveApplicationHttpKernel($app)
    {
        $app->singleton(HttpKernelContract::class, HttpKernel::class);
    }

    protected function resolveApplication()
    {
        static::$cacheApplicationBootstrapFile ??= $this->getApplicationBootstrapFile('app.php');

        if (\is_string(static::$cacheApplicationBootstrapFile)) {
            $_ENV['APP_BASE_PATH'] = $_ENV['APP_BASE_PATH'];

            return require static::$cacheApplicationBootstrapFile;
        }

        return $this->resolveGeccheApplication();
    }

    final protected function resolveGeccheApplication()
    {
        return (new ApplicationBuilder(new Application($_ENV['APP_BASE_PATH'])))
            ->withKernels()
            ->withProviders()
            ->withMiddleware(static function ($middleware) {
                //
            })
            ->withCommands()
            ->create();
    }

    protected function getPackageProviders($app)
    {
        return [
            TestServiceProvider::class,
            DomainConsoleServiceProvider::class,
        ];
    }

    public function testDomainConfigFileIsLoadedAtRuntime(): void
    {
        $_SERVER['SERVER_NAME'] = $this->site;

        $this->artisan('domain:add', ['domain' => $this->site]);
        $this->writeDomainConfig($this->site, [
            'app' => [
                'name' => $this->domainConfigMarker,
            ],
        ]);

        $this->refreshApplication();

        $this->assertEquals($this->domainConfigMarker, config('app.name'));
    }

    public function testConfigCacheIncludesDomainConfig(): void
    {
        $_SERVER['SERVER_NAME'] = $this->site;

        $this->artisan('domain:add', ['domain' => $this->site]);
        $this->writeDomainConfig($this->site, [
            'app' => [
                'name' => $this->domainConfigMarker,
            ],
        ]);

        $this->refreshApplication();

        $this->artisan('config:cache');

        $cachedConfigPath = $this->getCachedConfigPath($this->site);

        $this->assertFileExists($cachedConfigPath);
        $this->assertStringContainsString($this->domainConfigMarker, $this->files->get($cachedConfigPath));

        $this->artisan('config:clear');
    }

    public function testMissingDomainConfigFileDoesNotBreakBootstrap(): void
    {
        $_SERVER['SERVER_NAME'] = $this->site;

        $this->artisan('domain:add', ['domain' => $this->site]);
        $this->removeDomainConfig($this->site);

        $this->refreshApplication();

        $this->assertNotEmpty(config('app.name'));
    }

    protected function writeDomainConfig(string $domain, array $config): void
    {
        $directory = config_path('domains');

        if (! $this->files->isDirectory($directory)) {
            $this->files->makeDirectory($directory, 0755, true);
        }

        $path = $this->domainConfigPath($domain);

        $this->files->put($path, "<?php\n\nreturn ".var_export($config, true).";\n");
    }

    protected function removeDomainConfig(string $domain): void
    {
        $path = $this->domainConfigPath($domain);

        if ($this->files->exists($path)) {
            $this->files->delete($path);
        }
    }

    protected function removeCachedConfig(string $domain): void
    {
        $path = $this->getCachedConfigPath($domain);

        if ($this->files->exists($path)) {
            $this->files->delete($path);
        }
    }

    protected function domainConfigPath(string $domain): string
    {
        return config_path('domains' . DIRECTORY_SEPARATOR . domain_sanitized($domain) . '.php');
    }

    protected function getCachedConfigPath(string $domain): string
    {
        return base_path('bootstrap/cache/config-' . domain_sanitized($domain) . '.php');
    }
}
