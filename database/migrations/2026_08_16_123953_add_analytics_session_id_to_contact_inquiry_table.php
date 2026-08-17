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
        Schema::table('contact_inquiry', function (Blueprint $table) {
            // nullOnDelete (not cascade): an inquiry must survive retention
            // cleanup of its linked analytics session - only the link breaks.
            $table->foreignId('analytics_session_id')
                ->nullable()
                ->after('message')
                ->constrained('analytics_sessions')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contact_inquiry', function (Blueprint $table) {
            $table->dropForeign(['analytics_session_id']);
            $table->dropColumn('analytics_session_id');
        });
    }
};
