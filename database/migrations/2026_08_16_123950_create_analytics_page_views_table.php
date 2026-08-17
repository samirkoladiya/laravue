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
        Schema::create('analytics_page_views', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('analytics_sessions')->cascadeOnDelete();
            // Denormalized from session_id to avoid a join for unique-visitor
            // counts, the single most common dashboard query.
            $table->foreignId('visitor_id')->constrained('analytics_visitors')->cascadeOnDelete();

            // Route path only (no query string), so 191 is generous - kept
            // short because this column is indexed and MySQL caps indexable
            // key length.
            $table->string('path', 191);
            $table->string('title')->nullable();
            $table->timestamp('viewed_at');
            // Time-on-page - reserved for a future pass, unpopulated in v1.
            $table->unsignedInteger('duration_seconds')->nullable();

            // Write-once rows - no updated_at.
            $table->timestamp('created_at')->useCurrent();

            $table->index(['visitor_id', 'viewed_at']);
            $table->index(['path', 'viewed_at']);
            $table->index('viewed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('analytics_page_views');
    }
};
