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
        Schema::create('rotation_details', function (Blueprint $table) {
            $table->id();
            $table->enum('day', ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche']);
            $table->boolean('is_off')->default(false);
            $table->boolean('technicien')->default(false);
            $table->boolean('teletravail')->default(false);
            $table->time('debut_journee')->nullable();
            $table->time('debut_pause')->nullable();
            $table->time('fin_pause')->nullable();
            $table->time('fin_journee')->nullable();
            $table->foreignId('rotation_id')
                ->constrained('rotations')
                ->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rotation_details');
    }
};
