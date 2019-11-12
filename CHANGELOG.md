# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),

## [Unreleased]

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

[Unreleased]: https://github.com/gecche/laravel-multidomain/compare/v1.1.1...HEAD
[1.1.1]: https://github.com/gecche/laravel-multidomain/compare/v1.1.0...v1.1.1
[1.1.2]: https://github.com/gecche/laravel-multidomain/compare/v1.1.1...v1.1.2
[1.1.3]: https://github.com/gecche/laravel-multidomain/compare/v1.1.2...v1.1.3
[1.1.4]: https://github.com/gecche/laravel-multidomain/compare/v1.1.3...v1.1.4
[1.1.5]: https://github.com/gecche/laravel-multidomain/compare/v1.1.4...v1.1.5
[1.1.6]: https://github.com/gecche/laravel-multidomain/compare/v1.1.5...v1.1.6
[1.1.7]: https://github.com/gecche/laravel-multidomain/compare/v1.1.6...v1.1.7
