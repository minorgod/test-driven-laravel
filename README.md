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

NOTE: There is more extensive documentation regarding some alternative changes you can make to your tests to keep the original base TestsCase class unmodified. This way you could run newer style tests alongside the older style tests. You can see that documentation here:
[https://laravel.com/docs/5.4/upgrade](https://laravel.com/docs/5.4/upgrade)

However, since it's a pain to go there and scroll down 3/4 of the page to find it among the other documentation on that page, here is the relevant excerpt:

> ## Running Laravel 5.3 & 5.4 Tests In A Single Application
>
> First install the `laravel/browser-kit-testing` package:
>
> ```php
> composer require --dev laravel/browser-kit-testing "1.*"
> ```
>
> Once the package has been installed, create a copy of your `tests/TestCase.php` file and save it to your `tests` directory as `BrowserKitTestCase.php`. Then, modify the file to extend the `Laravel\BrowserKitTesting\TestCase` class. Once you have done this, you should have two base test classes in your `tests` directory: `TestCase.php` and `BrowserKitTestCase.php`. In order for your `BrowserKitTestCase` class to be properly loaded, you may need to add it to your `composer.json` file:
>
> ```php
> "autoload-dev": {
>     "psr-4": {
>         "Tests\\": "tests/"
>    }
> }
> ```
>
> Tests written on Laravel 5.3 will extend the `BrowserKitTestCase` class while any new tests that use the Laravel 5.4 testing layer will extend the `TestCase` class. Your `BrowserKitTestCase` class should look like the following:
>
> ```php
> <?php
> 
> use Illuminate\Contracts\Console\Kernel;
> use Laravel\BrowserKitTesting\TestCase as BaseTestCase;
> 
> abstract class BrowserKitTestCase extends BaseTestCase
> {
>     /**
>      * The base URL of the application.
>      *
>      * @var string
>      */
>     public $baseUrl = 'http://localhost';
> 
>     /**
>      * Creates the application.
>      *
>      * @return \Illuminate\Foundation\Application
>      */
>     public function createApplication()
>     {
>         $app = require __DIR__.'/../bootstrap/app.php';
> 
>         $app->make(Kernel::class)->bootstrap();
> 
>         return $app;
>     }
> }
> ```
>
> Once you have created this class, make sure to update all of your tests to extend your new `BrowserKitTestCase` class. This will allow all of your tests written on Laravel 5.3 to continue running on Laravel 5.4. If you choose, you can slowly begin to port them over to the new [Laravel 5.4 test syntax](https://laravel.com/docs/5.4/http-tests) or [Laravel Dusk](https://laravel.com/docs/5.4/dusk).
>
## Getting Started With Validation
In this section of the course, Adam shows how you can create a function to easily diable Laravel's built-in exception handling so you can see the real exceptions in your tests. To do this he creates a custom function in the TestCase class, but it will not quite work in Laravel 7. You need to pass a Throwable instead of an Exception to the `report` and `render` functions. Here's a Laravel 7 version of the method...
```php
/**
     * This overrides Laravel's automatic exception handling
     */
    protected function disableExceptionHandling() {
        $this->app->instance(ExceptionHandler::class, new class extends Handler{
            public function __construct(){}
            public function report(Throwable $e){}
            public function render($request, Throwable $e){
                throw $e;
            }
        });
    }
```

### In the email_is_required_to_purchase_tickets test...
...instead of the using the assertArrayHasKey function to make sure the response contains an email validation error message, use the assertJsonValidationErrors on the `$this->response` object....
```php
    /** @test */
    public function email_is_required_to_purchase_tickets(){

        // Arrange
        $paymentGateway = new FakePaymentGateway;
        // Bind the FakePaymentGateway class to the PaymentGateway interface so we can type hint the
        // interface in the controller methods.
        $this->app->instance(PaymentGateway::class, $paymentGateway);

        // Create a concert
        $concert = factory(Concert::class)->create();

        // Act
        // Purchase concert tickets
        $this->json('POST', "/concerts/{$concert->id}/orders", [
            'ticket_quantity' => 3,
            'payment_token' => $paymentGateway->getValidTestToken(),
        ]);

        // Laravel uses response code 422 for validation error responses
        $this->assertResponseStatus(422);

        // Assert that there are json validation errors
        $this->response->assertJsonValidationErrors('email');

    }

```
