<?php

return [

    /*
    |--------------------------------------------------------------------------
    | IP Hash Key
    |--------------------------------------------------------------------------
    |
    | Raw IP addresses are never stored. Every IP is hashed with this key
    | via HMAC-SHA256 before it touches the database. Kept separate from
    | APP_KEY so rotating APP_KEY doesn't silently break IP-hash dedup
    | continuity.
    |
    */

    'ip_hash_key' => env('ANALYTICS_IP_HASH_KEY', env('APP_KEY')),

    /*
    |--------------------------------------------------------------------------
    | Session Timing
    |--------------------------------------------------------------------------
    */

    'session_timeout_minutes' => 30,
    'online_window_minutes' => 5,

    /*
    |--------------------------------------------------------------------------
    | Data Retention (days)
    |--------------------------------------------------------------------------
    |
    | Raw event-level data is purged after these windows via the
    | `analytics:cleanup` command. Daily stats are kept forever - it's a
    | tiny, PII-free summary table.
    |
    */

    'retention' => [
        'page_views_days' => 400,
        'events_days' => 400,
        'sessions_days' => 400,
        'visitors_days' => 730,
        'daily_stats_days' => null,
    ],

    /*
    |--------------------------------------------------------------------------
    | Traffic Source Classification
    |--------------------------------------------------------------------------
    |
    | Domain/param lists used by TrafficSourceResolver. Extend these to add
    | new sources without touching code.
    |
    */

    'traffic_sources' => [
        'search_engines' => [
            'google.com', 'bing.com', 'yahoo.com', 'duckduckgo.com',
            'baidu.com', 'yandex.com', 'ecosia.org',
        ],

        'social' => [
            'facebook.com', 'instagram.com', 'x.com', 'twitter.com',
            'linkedin.com', 'pinterest.com', 'youtube.com', 't.co',
            'wa.me', 'whatsapp.com', 'reddit.com', 'tiktok.com',
        ],

        'paid_click_ids' => ['gclid', 'fbclid', 'msclkid', 'ttclid'],

        'paid_mediums' => ['cpc', 'ppc', 'paidsearch', 'paidsocial', 'display', 'banner'],
    ],

];
