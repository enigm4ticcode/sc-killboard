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
        Schema::table('kills', function (Blueprint $table) {
            // Composite index for leaderboard queries (killer-based)
            $table->index(['destroyed_at', 'type', 'killer_id'], 'kills_destroyed_type_killer_idx');

            // Composite index for leaderboard queries (victim-based)
            $table->index(['destroyed_at', 'type', 'victim_id'], 'kills_destroyed_type_victim_idx');

            // Composite index for weapon leaderboards
            $table->index(['destroyed_at', 'weapon_id'], 'kills_destroyed_weapon_idx');

            // Composite index for player kill history
            $table->index(['killer_id', 'destroyed_at'], 'kills_killer_destroyed_idx');

            // Composite index for player loss history
            $table->index(['victim_id', 'destroyed_at'], 'kills_victim_destroyed_idx');

            // Composite index for duplicate detection in findOrCreateKill()
            $table->index(['killer_id', 'victim_id', 'weapon_id', 'destroyed_at', 'type'], 'kills_duplicate_detection_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kills', function (Blueprint $table) {
            $table->dropIndex('kills_destroyed_type_killer_idx');
            $table->dropIndex('kills_destroyed_type_victim_idx');
            $table->dropIndex('kills_destroyed_weapon_idx');
            $table->dropIndex('kills_killer_destroyed_idx');
            $table->dropIndex('kills_victim_destroyed_idx');
            $table->dropIndex('kills_duplicate_detection_idx');
        });
    }
};
