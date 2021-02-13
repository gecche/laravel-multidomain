[![Laravel](https://img.shields.io/badge/Laravel-5.x-orange.svg?style=flat-square)](http://laravel.com)
[![Laravel](https://img.shields.io/badge/Laravel-6.x-orange.svg?style=flat-square)](http://laravel.com)
[![Laravel](https://img.shields.io/badge/Laravel-7.x-orange.svg?style=flat-square)](http://laravel.com)
[![Laravel](https://img.shields.io/badge/Laravel-8.x-orange.svg?style=flat-square)](http://laravel.com)
[![License](http://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](https://tldrlegal.com/license/mit-license)

# Laravel Multi Domain
An extension for using Laravel in a multi domain setting

![Laravel Multi Domain](laravel-multidomain.png)

## Description
This package allows a single Laravel installation to work with multiple HTTP domains.

There are many cases in which different customers use the same application in terms of code but not in terms of 
database, storage and configuration.

This package gives a very simple way to get a specific env file, a specific storage path and a specific database 
for each such customer.

## Documentation

### Version Compatibility

 Laravel  | Multidomain
:---------|:----------
 5.5.x    | 1.1.x
 5.6.x    | 1.2.x
 5.7.x    | 1.3.x
 5.8.x    | 1.4.x
 6.x      | 2.x
 7.x      | 3.x
 8.x      | 4.x

#### Further notes on Compatibility

Releases v1.1.x:
- From v1.1.0 to v1.1.5, releases are fully compatibile with Laravel 5.5, 5.6, 5.7, 5.8 or 6.0. 
- From v1.1.6+ releases v1.1.x are only compatible with Laravel 5.5 in order to run tests correctly.

To date, releases v1.1.6+, v1.2.x, v1.3.x, v1.4.x, v2.x and v3.x are functionally equivalent.
Releases have been separated in order to run integration tests with the corresponding version of the 
Laravel framework.

However, with the release of Laravel 8, releases v1.1.14, v1.2.8, v1.3.8 and v1.4.8 are the last releases 
including new features for the corresponding Laravel 5.x versions (bugfix support is still active for that versions). 
**2021-02-13 UPDATE**: some last features for v1.1+ releases are still ongoing :)

  
v1.0 requires Laravel 5.1, 5.2, 5.3 and 5.4 (no longer maintained and not tested versus laravel 5.4, 
however the usage of the package is the same as for 1.1)


### Installation

Add gecche/laravel-multidomain as a requirement to composer.json:

```javascript
{
    "require": {
        "gecche/laravel-multidomain": "4.*"
    }
}
```

Update your packages with composer update or install with composer install.

You can also add the package using `composer require gecche/laravel-multidomain` and later 
specify the version you want (for now, dev-v1.1.* is your best bet).

This package needs to override the detection of the HTTP domain in a minimal set of Laravel core functions 
at the very start of the bootstrap process in order to get the specific environment file. So this package 
needs a few more configuration steps than most Laravel packages. 

Installation steps:
1. replace the whole Laravel container by modifying the following lines
at the very top of the `bootstrap/app.php` file.

```php
//$app = new Illuminate\Foundation\Application(
$app = new Gecche\Multidomain\Foundation\Application(
    $_ENV['APP_BASE_PATH'] ?? dirname(__DIR__)
);
```

2. update the two application Kernels (HTTP and CLI).

At the very top of the `app/Http/Kernel.php` file , do the following change:

```php
//use Illuminate\Foundation\Http\Kernel as HttpKernel;
use Gecche\Multidomain\Foundation\Http\Kernel as HttpKernel;
```

Similarly in the `app/Console/Kernel.php` file:

```php
//use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Gecche\Multidomain\Foundation\Console\Kernel as ConsoleKernel;
```

3. Override the `QueueServiceProvider` with the extended 
one in the `$providers` array in the `config/app.php` file:

```php
        //Illuminate\Queue\QueueServiceProvider::class,
        Gecche\Multidomain\Queue\QueueServiceProvider::class,
```
        
4. publish the config file.

```
php artisan vendor:publish 
```

(This package makes use of the discovery feature.)

Following the above steps, your application will be aware of the HTTP domain
in which is running, both for HTTP and CLI requests, including queue support.


### Usage

This package adds three commands to manage your application HTTP domains:

#### `domain.add` artisan command

The main command is the `domain:add` command which takes as argument the name of the 
HTTP domain to add to the application. Let us suppose we have two domains, `site1.com` 
and `site2.com`, sharing the same code.

We simply do:

```
php artisan domain:add site1.com 
```

and 

```
php artisan domain:add site2.com 
```

These commands create two new environment files, `.env.site1.com` and `.env.site2.com`, 
in which you can put the specific configuration for each site (e.g. databases configuration, 
cache configuration and other configurations, as usually found in an environment file).

The command also adds an entry in the `domains` key in `config/domains.php` file.

In addition, two new folders are created, `storage/site1_com/` and `storage/site2_com/`. 
They have the same folder structure as the main storage.

Customizations to this `storage` substructure must be matched by values in the `config/domain.php` file.
 
#### `domain.remove` artisan command
The  `domain:remove` command removes the specified HTTP domain from the 
application by deleting its environment file.  E.g.:

```
php artisan domain:remove site2.com 
```
Adding the `force` option will delete the domain storage folder.

The command also removes the appropriate entry from, the `domains` key in `config/domains.php` file.

#### `domain.update_env` artisan command
The  `domain:update_env` command passes a json encoded array of data to update one or all of the environment files. 
These values will be added at the end of the appropriate .env.

Update a single domain environment file by adding the `domain` option. 

When the `domain` option is absent, the command updates all the environment files, including the standard `.env` one.

The list of domains to be updated is maintained in the `domain.php` config file. 

E.g.:
  
```
php artisan domain:update_env --domain_values='{"TOM_DRIVER":"TOMMY"}' 
```  
 
will add the line `TOM_DRIVER=TOMMY` to all the domain environment files.

#### `domain.list` artisan command
The  `domain:list` command lists the currently installed domains, with their .env file and storage path dir.

The list is maintained in the `domains` key of the `config/domain.php` config file.

This list is automatically updated at every `domain:add` and `domain:remove` commands run.

#### `config:cache` artisan command
The config:cache artisan command can be used with this package in the same way as any other 
artisan command. 

Note that this command will generate a file config.php file for each domain under which the command has been executed.
I.e. the command
 ```
 php artisan config:cache --domain=site2.com 
 ```
will generate the file
 ```
 config-site2_com.php 
 ```

### Further information
At run-time, the current HTTP domain is maintained in the laravel container 
and can be accessed by its `domain()` method added by this package.

A `domainList()` method is available. It returns an associative array 
containing the installed domains info, similar to the `domain.list` command above.

E.g.
 ```
 [ 
    site1.com => [
        'storage_path' => <LARAVEL-STORAGE-PATH>/site1_com,
        'env' => '.env.site1.com'
    ]
 ] 
 ```

#### Distinguishing between HTTP domains in web pages

For each HTTP request received by the application, the specific environment file is 
loaded and the specific storage folder is used.
 
If no specific environment file and/or storage folder is found, the standard one is used.

The detection of the right HTTP domain is done by using the `$_SERVER['SERVER_NAME']` 
PHP variable. 
 
#### Customizing the detection of HTTP domains

Starting from release 1.1.15, the detection of HTTP domains can be customized passing a `Closure` 
as the `domain_detection_function_web` entry of the new `domainParams` argument of `Application`'s 
constructor. In the following example, the HTTP domain detection relies upon `$_SERVER['HTTP_HOST']` 
instead of `$_SERVER['SERVER_NAME']`.

```php
$domainParams = [
    'domain_detection_function_web' => function() {
        return \Illuminate\Support\Arr::get($_SERVER,'HTTP_HOST');
    }
];

//$app = new Illuminate\Foundation\Application(
$app = new Gecche\Multidomain\Foundation\Application(
    $_ENV['APP_BASE_PATH'] ?? dirname(__DIR__), null, $domainParams
);
```


#### Using multi domains in artisan commands
 
In order to distinguishing between domains, each artisan command accepts a new option: `domain`. E.g.:

```
php artisan list --domain=site1.com 
```

The command will use the corresponding domain settings.

#### About queues
 
 The artisan commands `queue:work` and `queue:listen` commands have been updated
 to accept a new `domain` option.
 ```
 php artisan queue:work --domain=site1.com 
 ```
As usual, the above command will use the corresponding domain settings.

Keep in mind that if, for example, you are using the `database` driver and you have two domains sharing the same db,
you should use two distinct queues if you want to manage the jobs of each domain separately.

For example, you could: 
- put in your .env files a default queue for each domain, e.g. 
`QUEUE_DEFAULT=default1` for site1.com and `QUEUE_DEFAULT=default2` for site2.com
- update the `queue.php` config file by changing the default queue accordingly: 
```
'database' => [
    'driver' => 'database',
    'table' => 'jobs',
    'queue' => env('QUEUE_DEFAULT','default'),
    'retry_after' => 90,
],
```
        
- launch two distinct workers 
```
 php artisan queue:work --domain=site1.com --queue=default1
 ```
 and
```
 php artisan queue:work --domain=site1.com --queue=default2
 ```

Obviously, the same can be done for each other queue driver, apart from the `sync` driver.

#### `storage:link` command
 
If you make use of the `storage:link` command and you want a distinct symbolic link for each domain, you have to create 
them manually because to date such command always creates a link named `storage` and that name is hard coded in the 
command. Extending the `storage:link` command allowing to choose the name is outside the scope of this package 
(and I hope it will be done directly in future versions of Laravel).

A way to obtain multiple storage links could be the following.
Let us suppose to have two domains, namely `site1.com` and `site2.com` with associated storage folders 
`storage/site1_com` and `storage/site2_com`.

1. We manually create links for each domain: 

```
ln -s storage/site1_com/app/public public/storage-site1_com 
ln -s storage/site2_com/app/public public/storage-site2_com 
```

2. In `.env.site1.com` and `.env.site2.com` we add an entry, e.g., for the first domain: 

```
APP_PUBLIC_STORAGE=-site1_com
```

3. In the `filesystems.php` config file we change as follows:

```
'public' => [
    'driver' => 'local',
    'root' => storage_path('app/public'),
    'url' => env('APP_URL').'/storage'.env('APP_PUBLIC_STORAGE'),
    'visibility' => 'public',
],
```

Furthermore, if you are using the package in a Single Page Application (SPA) setting, you could better handling distinct 
public resources for each domain via .htaccess or similar solutions as pointed out by [Scaenicus](https://github.com/Scaenicus) in his 
[.htaccess solution](https://github.com/gecche/laravel-multidomain/issues/11#issuecomment-559822284).

#### Storing environment files in a custom folder

Starting from version 1.1.11 a second argument has been added to the Application constructor in order to choose the 
folder where to place the environment files: if you have tens of domains, it is not very pleasant to have environment 
files in the root Laravel's app folder. 

So, if you want to use a different folder simply add it at the very top of the `bootstrap/app.php` file. for example, 
if you want to add environment files to the `envs` subfolder, simply do:

```php
//$app = new Illuminate\Foundation\Application(
$app = new Gecche\Multidomain\Foundation\Application(
    $_ENV['APP_BASE_PATH'] ?? dirname(__DIR__),
    dirname(__DIR__) . DIRECTORY_SEPARATOR . 'envs'
);
```

If you do not specify the second argument, the standard folder is assumed. Please note that if you specify a folder, 
also the standard `.env` file has to be placed in it

#### Default environment files and storage folders

If you try to run a web page or an shell command under a certain domain, e.g. `sub1.site1.com` and there is no specific 
environment file for that domain, i.e. the file `.env.sub1.site1.com` does not exist, the package will use the first 
available environment file by splitting the domain name with dots. In this example, the package searches for the 
the first environment file among the followings:

```
.env.site1.com
.env.com
.env
```

The same logic applies to the storage folder as well.

#### About Laravel's Scheduler, Supervisor and some limitation
 
If in your setting you make use of the Laravel's Scheduler, remember that also the command `schedule:run` has to be 
launched with the domain option. Hence, you have to launch a scheduler for each domain. 
At first one could think that one Scheduler instance should handle the commands launched for any domain, but the 
Scheduler itself is run within a Laravel Application, so the "env" under which it is run, automatically applies to 
each scheduled command and the `--domain` option has no effect at all.

The same applies to externals tools like Supervisor: if you use Supervisor for artisan commands, e.g. the `queue:work` 
command, please be sure to prepare a command for each domain you want to handle.

Due to the above, there are some cases in which the package can't work: in those settings where you don't have the 
possibility of changing for example the supervisor configuration rather than the `crontab` entries for the scheduler. 
Such an example has been pointed out [here](https://github.com/gecche/laravel-multidomain/issues/21#issuecomment-600469868) 
in which a Docker instance has been used. 




