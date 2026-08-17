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
        Schema::create('analytics_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('analytics_sessions')->cascadeOnDelete();
            $table->foreignId('visitor_id')->constrained('analytics_visitors')->cascadeOnDelete();

            $table->string('event_name', 100);
            $table->json('event_data')->nullable();
            // The "leads" flag - only contact_form_submitted is true by
            // default; other events are engagement signals, not leads.
            $table->boolean('is_conversion')->default(false);

            $table->timestamp('occurred_at');
            $table->timestamp('created_at')->useCurrent();

            $table->index(['event_name', 'occurred_at']);
            $table->index(['is_conversion', 'occurred_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('analytics_events');
    }
};
