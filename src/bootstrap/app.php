<?php

use App\Http\Middleware\EnsureUserIsAdmin;
use App\Http\Middleware\EnsureUserIsGeneral;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => EnsureUserIsAdmin::class,
            'user' => EnsureUserIsGeneral::class,
        ]);

        $middleware->redirectGuestsTo('/login');

        $middleware->redirectUsersTo(function (Request $request) {
            if ($request->user()?->isAdmin()) {
                return route('admin.attendance.list');
            }

            if ($request->user() !== null && ! $request->user()->hasVerifiedEmail()) {
                return route('verification.notice');
            }

            return route('attendance');
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
