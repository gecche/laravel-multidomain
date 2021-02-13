# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),

## [Unreleased]

## 4.2 - 2021-02-13
### Added
- New `Application`'s constructor param `domainParams` for storing miscellaneous params used for handling domains:
  the first one is the `domain_detection_function_web` param which accept a `Closure` for customizing
  the doamin detection function instead of relying upon `$_SERVER['SERVER_NAME']` value
### Changed
- README.md updated

## 4.1 - 2020-11-17
### Changed
- Updated `queue:listen` command adapting it to the new Laravel 8.x signature
- Compatibility with Laravel Horizon

## 4.0 - 2020-10-07
### Changed
- `composer.json` updated for Laravel 8.x  

## 3.9 - 2021-02-13
### Added
- New `Application`'s constructor param `domainParams` for storing miscellaneous params used for handling domains:
  the first one is the `domain_detection_function_web` param which accept a `Closure` for customizing
  the doamin detection function instead of relying upon `$_SERVER['SERVER_NAME']` value
### Changed
- README.md updated

## 3.8 - 2020-10-07
### Changed
- New logic for default environment files and storage folders
- Tests improved and updated
- README.md updated
 
## 3.7 - 2020-05-24
### Changed
- README.md updated with some minor correction and frontend image. (I've done a mistake with tag names: 
from 3.2 to 3.7 instead of 3.3 :) )
 
## 3.2 - 2020-04-23
### Changed
- Improved robustness in `environmentFileDomain` when no domain is given.
 
## 3.1 - 2020-04-19
### Added
- An optional argument to the Application constructor for handling the storage of env files in a different folder 
than the root Laravel's app folder. With new tests.
- The `env_path` helper to get the folder in which env files are placed. 
### Changed
- README.md updated
 
## 3.0 - 2020-03-09
### Changed
- `composer.json` updated for Laravel 7.x  

## 2.9 - 2021-02-13
### Added
- New `Application`'s constructor param `domainParams` for storing miscellaneous params used for handling domains:
  the first one is the `domain_detection_function_web` param which accept a `Closure` for customizing
  the doamin detection function instead of relying upon `$_SERVER['SERVER_NAME']` value
### Changed
- README.md updated

## 2.8 - 2020-10-07
### Changed
- New logic for default environment files and storage folders
- Tests improved and updated
- README.md updated
 
## 2.7 - 2020-05-24
### Changed
- README.md updated with some minor correction and frontend image.
 
## 2.6 - 2020-04-23
### Changed
- Improved robustness in `environmentFileDomain` when no domain is given.
 
## 2.5 - 2020-04-19
### Added
- An optional argument to the Application constructor for handling the storage of env files in a different folder 
than the root Laravel's app folder. With new tests.
- The `env_path` helper to get the folder in which env files are placed. 
### Changed
- README.md updated
 
## 2.4 - 2020-03-09
### Changed
- Force the detection of the domain when trying to access to domain info if the domain has never been detected
- Improved tests 
  
## 2.3 - 2020-01-17
### Changed
- README.md: added notes on `storage:link` command
 
## 2.2 - 2019-11-15
### Changed
- Changed `Gecche\Multidomain\Foundation\Application` for handling separated cache files for each 
domain when using the `route:cache` or `event:cache` Laravel commands 

## 2.1 - 2019-11-12
### Changed
- Bugfix in `Gecche\Multidomain\Foundation\Console\DomainCommandTrait`: running the 
`domain:add` command, sometimes some lines in the stub .env file were 
skipped and not replicated in the new .env.<domain> file
 
## 2.0 - 2019-10-29
### Changed
- `composer.json` updated for Laravel 6.x  

## 1.4.9 - 2021-02-13
### Added
- New `Application`'s constructor param `domainParams` for storing miscellaneous params used for handling domains:
  the first one is the `domain_detection_function_web` param which accept a `Closure` for customizing
  the doamin detection function instead of relying upon `$_SERVER['SERVER_NAME']` value
### Changed
- README.md updated

## 1.4.8 - 2020-10-07
### Changed
- New logic for default environment files and storage folders
- Tests improved and updated
- README.md updated
 
## 1.4.7 - 2020-05-24
### Changed
- README.md updated with some minor correction and frontend image.
 
## 1.4.6 - 2020-04-23
### Changed
- Improved robustness in `environmentFileDomain` when no domain is given.
 
## 1.4.5 - 2020-04-19
### Added
- An optional argument to the Application constructor for handling the storage of env files in a different folder 
than the root Laravel's app folder. With new tests.
- The `env_path` helper to get the folder in which env files are placed. 
### Changed
- README.md updated
 
## 1.4.4 - 2020-03-09
### Changed
- Force the detection of the domain when trying to access to domain info if the domain has never been detected
- Improved tests 
  
## 1.4.3 - 2020-01-17
### Changed
- README.md: added notes on `storage:link` command
 
## 1.4.2 - 2019-11-15
### Changed
- Changed `Gecche\Multidomain\Foundation\Application` for handling separated cache files for each 
domain when using the `route:cache` or `event:cache` Laravel commands 

## 1.4.1 - 2019-11-12
### Changed
- Bugfix in `Gecche\Multidomain\Foundation\Console\DomainCommandTrait`: running the 
`domain:add` command, sometimes some lines in the stub .env file were 
skipped and not replicated in the new .env.<domain> file
 
## 1.4.0 - 2019-10-29
### Changed
- `composer.json` updated for Laravel 5.8  

## 1.3.9 - 2021-02-13
### Added
- New `Application`'s constructor param `domainParams` for storing miscellaneous params used for handling domains:
  the first one is the `domain_detection_function_web` param which accept a `Closure` for customizing
  the doamin detection function instead of relying upon `$_SERVER['SERVER_NAME']` value
### Changed
- README.md updated

## 1.3.8 - 2020-10-07
### Changed
- New logic for default environment files and storage folders
- Tests improved and updated
- README.md updated
 
## 1.3.7 - 2020-05-24
### Changed
- README.md updated with some minor correction and frontend image.
 
## 1.3.6 - 2020-04-23
### Changed
- Improved robustness in `environmentFileDomain` when no domain is given.
 
## 1.3.5 - 2020-04-19
### Added
- An optional argument to the Application constructor for handling the storage of env files in a different folder 
than the root Laravel's app folder. With new tests.
- The `env_path` helper to get the folder in which env files are placed. 
### Changed
- README.md updated
 
## 1.3.4 - 2020-03-09
### Changed
- Force the detection of the domain when trying to access to domain info if the domain has never been detected
- Improved tests 
  
## 1.3.3 - 2020-01-17
### Changed
- README.md: added notes on `storage:link` command
 
## 1.3.2 - 2019-11-15
### Changed
- Changed `Gecche\Multidomain\Foundation\Application` for handling separated cache files for each 
domain when using the `route:cache` Laravel command 

## 1.3.1 - 2019-11-12
### Changed
- Bugfix in `Gecche\Multidomain\Foundation\Console\DomainCommandTrait`: running the 
`domain:add` command, sometimes some lines in the stub .env file were 
skipped and not replicated in the new .env.<domain> file
 
## 1.3.0 - 2019-10-29
### Changed
- `composer.json` updated for Laravel 5.7  
- Bugfix in `Gecche\Multidomain\Queue\Listener` due to changes in handling 
worker commands in the parent class.

## 1.2.9 - 2021-02-13
### Added
- New `Application`'s constructor param `domainParams` for storing miscellaneous params used for handling domains:
  the first one is the `domain_detection_function_web` param which accept a `Closure` for customizing
  the doamin detection function instead of relying upon `$_SERVER['SERVER_NAME']` value
### Changed
- README.md updated

## 1.2.8 - 2020-10-06
### Changed
- New logic for default environment files and storage folders
- Tests improved and updated
- README.md updated
 
## 1.2.7 - 2020-05-24
### Changed
- README.md updated with some minor correction and frontend image.
 
## 1.2.6 - 2020-04-23
### Changed
- Improved robustness in `environmentFileDomain` when no domain is given.
 
## 1.2.5 - 2020-04-19
### Added
- An optional argument to the Application constructor for handling the storage of env files in a different folder 
than the root Laravel's app folder. With new tests.
- The `env_path` helper to get the folder in which env files are placed. 
### Changed
- README.md updated
 
## 1.2.4 - 2020-03-09
### Changed
- Force the detection of the domain when trying to access to domain info if the domain has never been detected
- Improved tests 
  
## 1.2.3 - 2020-01-17
### Changed
- README.md: added notes on `storage:link` command
 
## 1.2.2 - 2019-11-15
### Changed
- Changed `Gecche\Multidomain\Foundation\Application` for handling separated cache files for each 
domain when using the `route:cache` Laravel command 

## 1.2.1 - 2019-11-12
### Changed
- Bugfix in `Gecche\Multidomain\Foundation\Console\DomainCommandTrait`: running the 
`domain:add` command, sometimes some lines in the stub .env file were 
skipped and not replicated in the new .env.<domain> file
 
## 1.2.0 - 2019-10-29
### Changed
- `composer.json` updated for Laravel 5.6  

## 1.1.15 - 2021-02-13
### Added
- New `Application`'s constructor param `domainParams` for storing miscellaneous params used for handling domains:
  the first one is the `domain_detection_function_web` param which accept a `Closure` for customizing
  the doamin detection function instead of relying upon `$_SERVER['SERVER_NAME']` value
### Changed
- README.md updated

## 1.1.14 - 2020-10-06
### Changed
- New logic for default environment files and storage folders
- Tests improved and updated
- README.md updated
 
## 1.1.13 - 2020-05-24
### Changed
- README.md updated with some minor correction and frontend image.
 
## 1.1.12 - 2020-04-23
### Changed
- Improved robustness in `environmentFileDomain` when no domain is given.
 
## 1.1.11 - 2020-04-16
### Added
- An optional argument to the Application constructor for handling the storage of env files in a different folder 
than the root Laravel's app folder. With new tests.
- The `env_path` helper to get the folder in which env files are placed. 
### Changed
- README.md updated
 
## 1.1.10 - 2020-03-09
### Changed
- Force the detection of the domain when trying to access to domain info if the domain has never been detected
- Improved tests 
 
## 1.1.9 - 2020-01-17
### Changed
- README.md: added notes on `storage:link` command
 
## 1.1.8 - 2019-11-15
### Changed
- Changed `Gecche\Multidomain\Foundation\Application` for handling separated cache files for each 
domain when using the `route:cache` Laravel command
 
## 1.1.7 - 2019-11-12
### Changed
- Bugfix in `Gecche\Multidomain\Foundation\Console\DomainCommandTrait`: running the 
`domain:add` command, sometimes some lines in the stub .env file were 
skipped and not replicated in the new .env.<domain> file
 
## 1.1.6 - 2019-10-29
### Added
- Test suites
### Changed
- Namespace of `DomainConsoleServiceProvider` provider from 
`Gecche\Multidomain\Foundation` to `Gecche\Multidomain\Foundation\Providers` 
    in order to respect folder struture.
- `composer.json` file for testing purposes (from now Git branches are separated for each 
Laravel release starting from 5.5 and as pointed out in the docs)  

## 1.1.5 - 2019-09-19
### Changed
- Compatibility with Laravel 6.x (note that previous version 1.1.4 is also compatible with Laravel 6.0.x including [laravel/helpers](https://github.com/laravel/helpers) in the composer.json. file of your Laravel Application)   

## 1.1.4 - 2019-05-25
### Changed
- Better English in README.md :) Thanks to leadegroot.

## 1.1.3 - 2019-05-22
### Changed
- README.md improved with queue example

## 1.1.2 - 2019-03-23
### Added
- Added new domain:list artisan command displaying info of installed domains
- Added domainsList() method to Application

## 1.1.1 - 2019-03-10
### Added
- This CHANGELOG.md file.
- Added the handling of config:cache artisan command by multiple cache config files

## 1.1.0 - 2018-06-24
### Added
- Initial version for Laravel 5.5.

[Unreleased]: https://github.com/gecche/laravel-multidomain/compare/v4.2...HEAD
[4.2]: https://github.com/gecche/laravel-multidomain/compare/v4.1...v4.2
[4.1]: https://github.com/gecche/laravel-multidomain/compare/v4.0...v4.1
[4.0]: https://github.com/gecche/laravel-multidomain/compare/v3.8...v4.0
[3.9]: https://github.com/gecche/laravel-multidomain/compare/v3.8...v3.9
[3.8]: https://github.com/gecche/laravel-multidomain/compare/v3.7...v3.8
[3.7]: https://github.com/gecche/laravel-multidomain/compare/v3.2...v3.7
[3.2]: https://github.com/gecche/laravel-multidomain/compare/v3.1...v3.2
[3.1]: https://github.com/gecche/laravel-multidomain/compare/v3.0...v3.1
[3.0]: https://github.com/gecche/laravel-multidomain/compare/v2.4...v3.0
[2.9]: https://github.com/gecche/laravel-multidomain/compare/v2.8...v2.9
[2.8]: https://github.com/gecche/laravel-multidomain/compare/v2.7...v2.8
[2.7]: https://github.com/gecche/laravel-multidomain/compare/v2.6...v2.7
[2.6]: https://github.com/gecche/laravel-multidomain/compare/v2.5...v2.6
[2.5]: https://github.com/gecche/laravel-multidomain/compare/v2.4...v2.5
[2.4]: https://github.com/gecche/laravel-multidomain/compare/v2.3...v2.4
[2.3]: https://github.com/gecche/laravel-multidomain/compare/v2.2...v2.3
[2.2]: https://github.com/gecche/laravel-multidomain/compare/v2.1...v2.2
[2.1]: https://github.com/gecche/laravel-multidomain/compare/v2.0...v2.1
[2.0]: https://github.com/gecche/laravel-multidomain/compare/v1.4.0...v2.0
[1.4.9]: https://github.com/gecche/laravel-multidomain/compare/v1.4.8...v1.4.9
[1.4.8]: https://github.com/gecche/laravel-multidomain/compare/v1.4.7...v1.4.8
[1.4.7]: https://github.com/gecche/laravel-multidomain/compare/v1.4.6...v1.4.7
[1.4.6]: https://github.com/gecche/laravel-multidomain/compare/v1.4.5...v1.4.6
[1.4.5]: https://github.com/gecche/laravel-multidomain/compare/v1.4.4...v1.4.5
[1.4.4]: https://github.com/gecche/laravel-multidomain/compare/v1.4.3...v1.4.4
[1.4.3]: https://github.com/gecche/laravel-multidomain/compare/v1.4.2...v1.4.3
[1.4.2]: https://github.com/gecche/laravel-multidomain/compare/v1.4.1...v1.4.2
[1.4.1]: https://github.com/gecche/laravel-multidomain/compare/v1.4.0...v1.4.1
[1.4.0]: https://github.com/gecche/laravel-multidomain/compare/v1.3.0...v1.4.0
[1.3.9]: https://github.com/gecche/laravel-multidomain/compare/v1.3.8...v1.3.9
[1.3.8]: https://github.com/gecche/laravel-multidomain/compare/v1.3.7...v1.3.8
[1.3.7]: https://github.com/gecche/laravel-multidomain/compare/v1.3.6...v1.3.7
[1.3.6]: https://github.com/gecche/laravel-multidomain/compare/v1.3.5...v1.3.6
[1.3.5]: https://github.com/gecche/laravel-multidomain/compare/v1.3.4...v1.3.5
[1.3.4]: https://github.com/gecche/laravel-multidomain/compare/v1.3.3...v1.3.4
[1.3.3]: https://github.com/gecche/laravel-multidomain/compare/v1.3.2...v1.3.3
[1.3.2]: https://github.com/gecche/laravel-multidomain/compare/v1.3.1...v1.3.2
[1.3.1]: https://github.com/gecche/laravel-multidomain/compare/v1.3.0...v1.3.1
[1.3.0]: https://github.com/gecche/laravel-multidomain/compare/v1.2.0...v1.3.0
[1.2.9]: https://github.com/gecche/laravel-multidomain/compare/v1.2.8...v1.2.9
[1.2.8]: https://github.com/gecche/laravel-multidomain/compare/v1.2.7...v1.2.8
[1.2.7]: https://github.com/gecche/laravel-multidomain/compare/v1.2.6...v1.2.7
[1.2.6]: https://github.com/gecche/laravel-multidomain/compare/v1.2.5...v1.2.6
[1.2.5]: https://github.com/gecche/laravel-multidomain/compare/v1.2.4...v1.2.5
[1.2.4]: https://github.com/gecche/laravel-multidomain/compare/v1.2.3...v1.2.4
[1.2.3]: https://github.com/gecche/laravel-multidomain/compare/v1.2.2...v1.2.3
[1.2.2]: https://github.com/gecche/laravel-multidomain/compare/v1.2.1...v1.2.2
[1.2.1]: https://github.com/gecche/laravel-multidomain/compare/v1.2.0...v1.2.1
[1.2.0]: https://github.com/gecche/laravel-multidomain/compare/v1.1.6...v1.2.0
[1.1.15]: https://github.com/gecche/laravel-multidomain/compare/v1.1.14...v1.1.15
[1.1.14]: https://github.com/gecche/laravel-multidomain/compare/v1.1.13...v1.1.14
[1.1.13]: https://github.com/gecche/laravel-multidomain/compare/v1.1.12...v1.1.13
[1.1.12]: https://github.com/gecche/laravel-multidomain/compare/v1.1.11...v1.1.12
[1.1.11]: https://github.com/gecche/laravel-multidomain/compare/v1.1.10...v1.1.11
[1.1.10]: https://github.com/gecche/laravel-multidomain/compare/v1.1.9...v1.1.10
[1.1.9]: https://github.com/gecche/laravel-multidomain/compare/v1.1.8...v1.1.9
[1.1.8]: https://github.com/gecche/laravel-multidomain/compare/v1.1.7...v1.1.8
[1.1.7]: https://github.com/gecche/laravel-multidomain/compare/v1.1.6...v1.1.7
[1.1.6]: https://github.com/gecche/laravel-multidomain/compare/v1.1.5...v1.1.6
[1.1.5]: https://github.com/gecche/laravel-multidomain/compare/v1.1.4...v1.1.5
[1.1.4]: https://github.com/gecche/laravel-multidomain/compare/v1.1.3...v1.1.4
[1.1.3]: https://github.com/gecche/laravel-multidomain/compare/v1.1.2...v1.1.3
[1.1.2]: https://github.com/gecche/laravel-multidomain/compare/v1.1.1...v1.1.2
[1.1.1]: https://github.com/gecche/laravel-multidomain/compare/v1.1.0...v1.1.1
