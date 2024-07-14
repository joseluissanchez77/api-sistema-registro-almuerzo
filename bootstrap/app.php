<?php

use App\Exceptions\ConflictException;
use App\Exceptions\DatabaseException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Traits\RestResponse;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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

        $exceptions->renderable(function (Throwable $exception, $request) {
            
            $baseController = new BaseController();

            if ($exception instanceof NotFoundHttpException) {
                $code = $exception->getStatusCode();
                // return $this->error($request->getPathInfo(), $exception, $exception->getMessage(), $code);
                return $baseController->error($request->getPathInfo(), $exception, __('messages.not-found', [], config('app.locale')), $code);
           
            }

            if ($exception instanceof AuthenticationException) {
                return $baseController->error($request->getPathInfo(), $exception, __('messages.no-credentials', [], config('app.locale')), Response::HTTP_UNAUTHORIZED);
            }
            
            if ($exception instanceof ValidationException) {
                $errors = $exception->validator->errors()->all();
             
                return $baseController->error($request->getPathInfo(), $exception,
                    $errors, Response::HTTP_BAD_REQUEST);
            }

            if ($exception instanceof DatabaseException) {
                dd(61515);
            }
        });
        // $exceptions->reportable(function (ConflictException $exception) {
        //     if ($exception instanceof ConflictException) {
        //         $code = $exception->getStatusCode();
        //         $message = $exception->getMessage();
        //         $baseController = new BaseController();
        //         return $baseController->error(request()->getPathInfo(), $exception, $message, $code);
        //     }
        // });
    })->create();
