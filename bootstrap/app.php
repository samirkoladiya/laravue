<?php

use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            HandleInertiaRequests::class,
        ]);

        // No `login`/`dashboard` routes are defined (auth lives under
        // /admin), so point the `auth`/`guest` middleware's default
        // redirects at their admin equivalents.
        $middleware->redirectGuestsTo('/admin/login');
        $middleware->redirectUsersTo('/admin/dashboard');

        // See the route comment in routes/web.php for why this endpoint
        // must be CSRF-exempt.
        $middleware->validateCsrfTokens(except: [
            'analytics/track',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        // Rate-limited form submissions (login, OTP, etc.) would otherwise
        // render Laravel's raw "Too Many Requests" error page inside
        // Inertia's error modal. Redirect back with a friendly, specific
        // wait time instead, using the same session `error` flash the
        // pages already read.
        $exceptions->render(function (ThrottleRequestsException $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return null;
            }

            $seconds = (int) ($e->getHeaders()['Retry-After'] ?? 60);
            $wait = $seconds >= 60
                ? ceil($seconds / 60).' minute'.(ceil($seconds / 60) > 1 ? 's' : '')
                : $seconds.' second'.($seconds !== 1 ? 's' : '');

            return back()->with('error', "Too many attempts. Please try again in {$wait}.");
        });
    })->create();
