<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;

return Application::configure(basePath: dirname(__DIR__))

    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )

    ->withMiddleware(function ($middleware) {

        $middleware->alias([

            // Middleware Admin
            'admin' =>
                \App\Http\Middleware\AdminMiddleware::class,

            // Middleware Redirect ke Register
            'registered' =>
                \App\Http\Middleware\RedirectIfNotRegistered::class,

        ]);

    })

    ->withExceptions(function ($exceptions) {
        //
    })

    ->create();
