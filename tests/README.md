
# laravel-multidomain (tests' notes)
We provide some information about laravel-multidomain's tests.

## Organization
We provide 3 test suites:
 
 1. `CommandsTestCase` where we include a test for each of the commands 
 provided by the `DomainConsoleServiceProvider` provider.
  
 2. `HttpTestCase` where we include some tests for simulating HTTP requests 
 changing the domain name. We provide two distinct settings (other than the default one) 
 for two additional domains, namely `site1.test` and `site2.test`.
 
 3. `ArtisanTestCase` where we include some tests for simulating the `--domain` 
 option applied to Artisan commands.
 
## Requirements and setup
We make use of the fantastic [Orchestra/Testbench](https://github.com/orchestral/testbench) 
package together with its browser-kit extension.
 
However, in order to setup the Laravel application with the machinery provided 
by our package, we provide a standard `.env.example` file and some configuration files 
to be used by the simulated Orchestra's Laravel application.

Moreover we created an adapted copy of the Laravel's `artisan` script in order to 
  use the `--domain` option in the Artisan commands launched by shell.

  Note that, as pointed out in the package documentation, the `--domain` option does 
  not work from within a Laravel application, so we needed to simulate command launches 
  directly from shell.

We perform above operations in the `setUp` sections of the suites.

We provide tests under the `mysql` connection (set in `phpunit.xml`). 
You need to create two databases, namely (`site1` and `site2`) in your DB server 
in order to run tests correctly.

## Running tests
As usual, we run tests from the package folder launching:

```bash
../../../vendor/bin/phpunit
```

However, as we need to simulate more than one domain via `$_SERVER['SERVER_NAME']` values 
and as in the `phpunit.xml` file we can't put a conditional environmentt variable, 
 in order to fullyperform all the tests, we require to launch also the following 
 commands:
  
```bash
SERVER_NAME=site1.test ../../../vendor/bin/phpunit
```

```bash
SERVER_NAME=site2.test ../../../vendor/bin/phpunit
```
