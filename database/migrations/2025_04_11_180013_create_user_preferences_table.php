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
        Schema::create('user_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('theme_mode')->default('auto'); // auto, light, dark
            $table->string('primary_color')->nullable();
            $table->string('accent_color')->nullable();
            $table->json('dashboard_layout')->nullable(); // Store layout configuration
            $table->json('planning_widgets')->nullable(); // Store planning widgets configuration
            $table->json('additional_settings')->nullable(); // For future extensions
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_preferences');
    }
};
