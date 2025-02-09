<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Routing\Middleware\SubstituteBindings;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->group('web', [SubstituteBindings::class]);
    })
    ->withCommands([
        App\Worker\Interface\Command\MessagingSubscribeCommand::class,
    ])
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
