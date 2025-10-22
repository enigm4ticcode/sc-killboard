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
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('rsi_verified')->default(false)->after('verified');
            $table->string('rsi_verification_key')->nullable()->after('verified');
            $table->timestamp('rsi_verified_at')->nullable()->after('verified');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('rsi_verified');
            $table->dropColumn('rsi_verification_key');
            $table->dropColumn('rsi_verified_at');
        });
    }
};
