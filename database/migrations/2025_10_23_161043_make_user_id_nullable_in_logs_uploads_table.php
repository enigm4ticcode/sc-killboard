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
        $isSqlite = Schema::getConnection()->getDriverName() === 'sqlite';

        Schema::table('log_uploads', function (Blueprint $table) use ($isSqlite): void {
            if (! $isSqlite) {
                $table->dropForeign('log_uploads_user_id_foreign');
            }

            $table->unsignedBigInteger('user_id')->nullable()->change();
        });

        Schema::table('kills', function (Blueprint $table) use ($isSqlite): void {
            if (! $isSqlite) {
                $table->dropForeign('user_id_foreign');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('log_uploads', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->change();
            $table->foreign('user_id', 'log_uploads_user_id_foreign')->references('id')->on('users');
        });

        Schema::table('kills', function (Blueprint $table) {
            $table->foreign('user_id', 'user_id_foreign')->references('id')->on('users');
        });
    }
};
