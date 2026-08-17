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

        // Keyed on visitor_id first (falls back to IP): more forgiving to
        // a single real visitor browsing normally - whose page views/
        // events could plausibly exceed a per-IP limit shared with other
        // visitors behind the same NAT/office network - while still
        // capping any single client's flood.
        RateLimiter::for('analytics', function (Request $request) {
            return Limit::perMinute(60)->by($request->input('visitor_id', $request->ip()));
        });
    }
}
