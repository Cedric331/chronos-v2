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
        Schema::create('team_params', function (Blueprint $table) {
            $table->id();
            $table->json('type_day');
            $table->boolean('module_alert')->default(false);
            $table->boolean('update_planning')->default(false);
            $table->boolean('share_link_planning')->default(true);
            $table->boolean('share_link')->default(true);
            $table->string('color1')->nullable();
            $table->string('color2')->nullable();
            $table->string('color3')->nullable();
            $table->string('color4')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('team_params');
    }
};
