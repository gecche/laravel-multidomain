# laravel-multidomain
A Laravel extension for using a laravel application on a multi domain setting

## Description
This package allows to use a single laravel installation to work with multiple HTTP domains.
There are many cases in which different customers use the same application in terms of code but not in terms of 
database, storage and configuration.
With the use of this package is very simple to get a specific env file, a specific storage path and a specific database 
for each such a customer.

## Documentation

### Installation

Add gecche/laravel-multidomain as a requirement to composer.json:

```javascript
{
    "require": {
        "gecche/laravel-multidomain": "1.1.*"
    }
}
```

Update your packages with composer update or install with composer install.

You can also add the package using composer require gecche/laravel-multidomain and later specifying the version you want (for now, dev-v1.1.* is your best bet).

This package needs to override a minimal set of Laravel core functions as the 
detection of the HTTP domain in which the application is running is needed at the very first of the bootstrap process in order 
to get the specific environment file.

That premise implies that this package needs a bit more of "configuration steps" 
with respect to a standard laravel package. 

The first action to take is to replace the whole Laravel container by modifying the following lines at the very top of 
the `app.php` file in the `bootstrap` folder.

```php
//$app = new Illuminate\Foundation\Application(
$app = new Gecche\Multidomain\Foundation\Application(
    realpath(__DIR__.'/../')
);
```

Then update also the two application Kernels (HTTP and CLI).

At the very top of the `Kernel.php` file in the `app\Http` folder, do the following changes:

```php
use Gecche\Multidomain\Foundation\Http\Kernel as HttpKernel;
//use Illuminate\Foundation\Http\Kernel as HttpKernel;
```

Similarly in the `Kernel.php` file in the `app\Console` folder:

```php
use Gecche\Multidomain\Foundation\Console\Kernel as ConsoleKernel;
#use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
```

The next step is to override the `QueueServiceProvider` with the extended 
one in the `$providers` array in the `app.php` file in the `config` folder:

```php
        //Illuminate\Queue\QueueServiceProvider::class,
        Gecche\Multidomain\Queue\QueueServiceProvider::class,
```
        
Lastly you publish the config file.

```
php artisan vendor:publish 
```

Following the above steps your application will be aware of the HTTP domain
in which is running both for HTTP and CLI requests (including queue support).

### Usage


## Compatibility

v1.1 requires Laravel 5.5+
v1.0 requires Laravel 5.1+ (no longer maintained and not tested versus laravel 5.4)