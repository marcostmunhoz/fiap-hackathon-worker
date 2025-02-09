<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
    )
    ->withCommands([
        App\Worker\Interface\Command\MessagingSubscribeCommand::class,
    ])
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
