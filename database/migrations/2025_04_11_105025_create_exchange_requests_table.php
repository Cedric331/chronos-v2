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
        Schema::create('exchange_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('requester_id')
                ->comment('Utilisateur qui demande l\'échange')
                ->constrained('users')
                ->cascadeOnDelete();
            $table->foreignId('requested_id')
                ->comment('Utilisateur à qui on demande l\'échange')
                ->constrained('users')
                ->cascadeOnDelete();
            $table->foreignId('requester_planning_id')
                ->comment('Planning que le demandeur veut échanger')
                ->constrained('plannings')
                ->cascadeOnDelete();
            $table->foreignId('requested_planning_id')
                ->comment('Planning que le demandeur souhaite obtenir')
                ->constrained('plannings')
                ->cascadeOnDelete();
            $table->foreignId('team_id')
                ->constrained('teams')
                ->cascadeOnDelete();
            $table->enum('status', ['pending', 'accepted', 'rejected', 'cancelled'])
                ->default('pending');
            $table->text('requester_comment')->nullable();
            $table->text('requested_comment')->nullable();
            $table->timestamp('responded_at')->nullable();
            $table->foreignId('approved_by')
                ->nullable()
                ->comment('Coordinateur qui a approuvé l\'échange')
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exchange_requests');
    }
};
