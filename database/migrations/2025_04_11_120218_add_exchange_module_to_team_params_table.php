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
        Schema::table('team_params', function (Blueprint $table) {
            $table->boolean('exchange_module')->default(true)->after('paid_leave');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('team_params', function (Blueprint $table) {
            $table->dropColumn('exchange_module');
        });
    }
};
