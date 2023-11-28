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
            $table->boolean('paid_leave')->default(false)->after('share_link');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('team_params', function (Blueprint $table) {
            $table->dropColumn('paid_leave');
        });
    }
};
