<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use App\Exceptions\Handler;
use Illuminate\Contracts\Debug\ExceptionHandler;
// use Laravel\BrowserKitTesting\TestCase as BaseTestCase;
use Throwable;

/**
 * Class TestCase
 * @package Tests
 */
abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    public $baseUrl = 'http://localhost';

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
}
