<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| This file is responsible for building the main application instance. In
| Laravel 12 the traditional console and HTTP kernels have been removed
| and replaced with a more streamlined configuration API. Here you
| define your routing files, middleware groups and exception handling.
|
*/

return Application::configure(basePath: dirname(__DIR__))
    // Register the routing files for the web UI and Artisan commands.
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    // Configure middleware for the application. The web group includes
    // session state, CSRF protection and cookie encryption by default.
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web();
        $middleware->api(prepend: [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        ]);
        $middleware->alias([
            'employee.access' => \App\Http\Middleware\CheckEmployeeAccess::class,
            'auth.users' => \App\Http\Middleware\AuthenticateUsers::class,
        ]);
    })
    // You may register additional console commands by scanning directories
    // or explicitly loading classes. For now the default configuration is
    // sufficient for a small application.
    ->withCommands([
        // __DIR__.'/../app/Console/Commands',
    ])
    // Global exception handling configuration can be customised here.
    ->withExceptions(function (Exceptions $exceptions) {
        // Register exception handling callbacks if necessary.
    })
    ->create();