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
        Schema::create('plannings', function (Blueprint $table) {
            $table->id();
            $table->string('type_day')->default('Travaille');
            $table->time('debut_journee')->nullable();
            $table->time('debut_pause')->nullable();
            $table->time('fin_pause')->nullable();
            $table->time('fin_journee')->nullable();
            $table->boolean('is_technician')->default(false);
            $table->boolean('telework')->default(false);
            $table->foreignId('rotation_id')
                ->nullable()
                ->constrained('rotations');
            $table->foreignId('calendar_id')
                ->constrained('calendars')
                ->cascadeOnDelete();
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plannings');
    }
};
