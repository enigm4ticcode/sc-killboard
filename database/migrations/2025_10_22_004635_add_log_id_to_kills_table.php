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
            $table->unsignedBigInteger('log_id')->nullable()->after('id');
            $table->foreign('log_id', 'log_id_foreign')
                ->references('id')
                ->on('log_uploads');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kills', function (Blueprint $table) {
            $table->dropForeign('log_id_foreign');
            $table->dropColumn('log_id');
        });
    }
};
