<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\IsAdmin;
use App\Http\Middleware\IsBarangay;
use App\Http\Middleware\IsStaff;
use App\Http\Middleware\IsAdminOrStaff;
use App\Http\Middleware\IsBeneficiary;
use App\Http\Middleware\PreventBackHistory;


return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withProviders([
        Barryvdh\DomPDF\ServiceProvider::class,
    ])
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'isAdmin' => IsAdmin::class,
        ]);

        $middleware->redirectGuestsTo('/');
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
    'isAdmin'        => IsAdmin::class,
    'isBarangay'     => IsBarangay::class,
    'isStaff'        => IsStaff::class,
    'isAdminOrStaff' => IsAdminOrStaff::class,
    'isBeneficiary'  => IsBeneficiary::class,
    'preventBackHistory' => PreventBackHistory::class,
]);

    $middleware->redirectGuestsTo('/');
})
    ->create();