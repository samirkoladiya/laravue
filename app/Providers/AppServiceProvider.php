<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);

        // Keyed on IP, not the client-supplied visitor_id: this endpoint is
        // CSRF-exempt (see routes/web.php), and visitor_id is untrusted
        // input - keying the limiter on it would let a single caller mint a
        // fresh id per request and bypass the cap entirely, while each new
        // id also rows an insert in analytics_visitors/analytics_sessions
        // (VisitorIdentityService), turning that bypass into unbounded DB
        // growth. IP is the only part of this request an attacker can't
        // freely rotate.
        RateLimiter::for('analytics', function (Request $request) {
            return Limit::perMinute(60)->by($request->ip());
        });
    }
}
