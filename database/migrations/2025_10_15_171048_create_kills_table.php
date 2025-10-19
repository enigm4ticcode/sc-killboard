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
        Schema::create('kills', function (Blueprint $table) {
            $table->id();
            $table->timestamp('destroyed_at');
            $table->unsignedBigInteger('ship_id')->nullable()->index();
            $table->foreign('ship_id')->references('id')->on('ships');
            $table->unsignedBigInteger('weapon_id')->index();
            $table->foreign('weapon_id')->references('id')->on('weapons');
            $table->unsignedBigInteger('victim_id')->index();
            $table->foreign('victim_id')->references('id')->on('players');
            $table->unsignedBigInteger('killer_id')->index();
            $table->foreign('killer_id')->references('id')->on('players');
            $table->enum('type', [\App\Models\Kill::TYPE_VEHICLE, \App\Models\Kill::TYPE_FPS])
                ->default(\App\Models\Kill::TYPE_VEHICLE)
                ->index();
            $table->string('location')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kills');
    }
};
