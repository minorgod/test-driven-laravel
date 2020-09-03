<?php

namespace Tests;
use App\Exceptions\Handler;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Laravel\BrowserKitTesting\TestCase as BaseTestCase;
use Laravel\BrowserKitTesting\TestResponse;
use Throwable;

/**
 * Class BrowserKitTestCase
 * @package Tests
 */
abstract class BrowserKitTestCase extends BaseTestCase
{

    use CreatesApplication;

    /**
     * The base URL of the application.
     *
     * @var string
     */
    public $baseUrl = 'http://localhost';


    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

    }

    /**
     * This overrides Laravel's automatic exception handling
     */
    protected function disableExceptionHandling() {
        $this->app->instance(ExceptionHandler::class, new class extends Handler{
            public function __construct(){}

            /**
             * Report or log an exception.
             *
             * @param  \Throwable  $e
             * @return void
             *
             * @throws \Exception
             */
            public function report(Throwable $e){}

            /**
             * Render an exception into an HTTP response.
             *
             * @param \Illuminate\Http\Request $request
             * @param \Throwable $e
             *
             * @throws \Throwable
             */
            public function render($request, Throwable $e){
                throw $e;
            }
        });
    }
}
