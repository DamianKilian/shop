<?php

use App\Http\Controllers\LogController;
use App\Http\Middleware\Localization;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->encryptCookies(except: [
            'app_locale',
        ]);
        $middleware->web(append: [
            Localization::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        if (env('LOG_HTTP')) {
            $exceptions->render(function (HttpException $e, Request $request) {
                $msg = $e->getStatusCode() . ': ' . $request->url() . ' (' . url()->previous() . ')';
                LogController::log($msg, 'log_http');
            });
        }
    })->create();
