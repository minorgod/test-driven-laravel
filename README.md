<p align="center">
<a href="https://travis-ci.org/laravel/framework"><img src="https://travis-ci.org/laravel/framework.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://poser.pugx.org/laravel/framework/d/total.svg" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://poser.pugx.org/laravel/framework/v/stable.svg" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://poser.pugx.org/laravel/framework/license.svg" alt="License"></a>
</p>

# About Test Driven Laravel
This repository is not the course! The course was written by Adam Wathan and can be purchased from him here:
https://testdrivenlaravel.com/

If you have not purchased the course, this repository will be of little to no value for you. This repository is simply where I'm working through the course content and documenting a few issues I encounter along the way. 

First and foremost is the issue of doing the course using Laravel 7.x. It was originally created for Laravel 5.3. I'm running through the course using Laravel 7.x (version 7.24 at the time of this writing) and have run into a few problems simply getting started. The course itself covers upgrading to Laravel 5.4, then 5.5, but this seemed a silly exercise to me since I'm creating all my new projects in Laravel 7.x and wanted to put my new knowledge directly to work. And so here's what you need to do to take the course in Laravel 7.x. 

## Add the `browser-kit-testing` composer package

The browser-kit-testing package allows you to use older Laravel 5.3 style testing functionality. Much of that functionality was migrated to other packages in later releases. Specifically "Frontend" tests were largely migrated to Laravel Dusk which runs a full-on headless browser, but Dusk is actually much much slower for running tests that only need to make simple HTTP requests. At least initially we won't need to be testing javascript or intricate frontend layout stuff, so it makes perfect sense to keep using the old style tests. In newer Laravel releases, the same sorts of simple HTTP tests are still possible without using Dusk, but the syntax is slightly different so, for compatibility with this course, just install browser-kit-testing and live with knowing you might want to use slightly different testing libs with slightly different syntax in the future. 

```
composer require laravel/browser-kit-testing --dev
```

## Modify your application's base `TestCase` class 

Tweak `/tests/TestCase.php` to extend `Laravel\BrowserKitTesting\TestCase` instead of `Illuminate\Foundation\Testing\TestCase` and add the $base_url property. In the end, your TestCase.php should look like this:

```php
<?php
namespace Tests;
// Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Laravel\BrowserKitTesting\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    public $baseUrl = 'http://localhost';
    // ...
}
```

Now run `composer dump-autoload` just to be sure. 

Now run `artisan test` and your tests should start running properly. I mean, they will fail properly and you can get down to business writing the code to make them pass. Happy coding!

