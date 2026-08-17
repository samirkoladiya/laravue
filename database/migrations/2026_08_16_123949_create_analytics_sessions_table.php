<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('analytics_sessions', function (Blueprint $table) {
            $table->id();
            $table->uuid('session_uuid')->unique();
            $table->foreignId('visitor_id')->constrained('analytics_visitors')->cascadeOnDelete();

            $table->timestamp('started_at');
            $table->timestamp('last_activity_at');
            $table->timestamp('ended_at')->nullable();
            $table->unsignedInteger('duration_seconds')->nullable();

            $table->string('entry_page', 2048);
            $table->string('exit_page', 2048)->nullable();
            $table->unsignedSmallInteger('page_view_count')->default(0);
            $table->boolean('is_bounce')->nullable();

            $table->string('device_type', 20);
            $table->string('browser', 50)->nullable();
            $table->string('browser_version', 20)->nullable();
            $table->string('os', 50)->nullable();
            $table->string('os_version', 20)->nullable();
            $table->unsignedSmallInteger('screen_width')->nullable();
            $table->unsignedSmallInteger('screen_height')->nullable();

            $table->string('traffic_source', 20);
            $table->string('referrer_domain')->nullable();
            $table->string('referrer_url', 2048)->nullable();
            $table->string('utm_source', 100)->nullable();
            $table->string('utm_medium', 100)->nullable();
            $table->string('utm_campaign', 100)->nullable();
            $table->string('utm_term', 100)->nullable();
            $table->string('utm_content', 100)->nullable();

            // HMAC-SHA256 of the client IP - never the raw address. Used only
            // for rate-limiting/abuse dedup, never surfaced in the dashboard.
            $table->char('ip_hash', 64);

            // Reserved for a future GeoIP pass - not populated in v1.
            $table->string('country', 2)->nullable();
            $table->string('city', 100)->nullable();

            $table->timestamps();

            $table->index('visitor_id');
            $table->index('last_activity_at');
            $table->index('started_at');
            $table->index(['traffic_source', 'started_at']);
            $table->index('ip_hash');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('analytics_sessions');
    }
};
