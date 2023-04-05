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
        Schema::create('hub_params', function (Blueprint $table) {
            $table->id();
            $table->json('type_day');
            $table->boolean('update_planning')->default(false);
            $table->foreignId('hub_id')
                ->constrained('hubs')
                ->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hub_params');
    }
};
