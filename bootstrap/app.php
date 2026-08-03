<?php

use App\Http\Middleware\CheckRole;
use App\Http\Middleware\SecurityHeaders;
use App\Http\Middleware\SetBranchContext;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => CheckRole::class,
            'security.headers' => SecurityHeaders::class,
            'branch.context' => SetBranchContext::class,
        ]);

        $middleware->appendToGroup('web', [
            SecurityHeaders::class,
            SetBranchContext::class,
        ]);

        $middleware->validateCsrfTokens(except: [
            'payments/webhook/xendit',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
        $exceptions->dontReportDuplicates();
    })
    ->create();
