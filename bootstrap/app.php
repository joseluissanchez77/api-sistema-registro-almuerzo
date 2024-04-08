<?php

use App\Exceptions\ConflictException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Traits\RestResponse;

class BaseController
{
    use RestResponse;
}

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        apiPrefix: '/api',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {

        $exceptions->reportable(function (ConflictException $exception) {
            if ($exception instanceof ConflictException) {
                $code = $exception->getStatusCode();
                $message = $exception->getMessage();
                $baseController = new BaseController();
                return $baseController->error(request()->getPathInfo(), $exception, $message, $code);
            }
        });
    })->create();
