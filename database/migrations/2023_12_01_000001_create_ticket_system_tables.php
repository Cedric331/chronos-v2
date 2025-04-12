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
        // Table des statuts de tickets
        Schema::create('ticket_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('color')->default('#6c757d'); // Couleur par défaut grise
            $table->string('icon')->nullable();
            $table->integer('order')->default(0); // Pour l'ordre d'affichage
            $table->boolean('is_default')->default(false);
            $table->boolean('is_closed')->default(false); // Si le statut signifie que le ticket est fermé
            $table->timestamps();
        });

        // Table des catégories de tickets
        Schema::create('ticket_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('color')->default('#6c757d');
            $table->string('icon')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });

        // Table des priorités de tickets
        Schema::create('ticket_priorities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('color')->default('#6c757d');
            $table->string('icon')->nullable();
            $table->integer('level')->default(0); // Plus le niveau est élevé, plus la priorité est haute
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });

        // Table principale des tickets
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->foreignId('status_id')->constrained('ticket_statuses');
            $table->foreignId('category_id')->constrained('ticket_categories');
            $table->foreignId('priority_id')->constrained('ticket_priorities');
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('assigned_to')->nullable()->constrained('users');
            $table->foreignId('team_id')->nullable()->constrained('teams');
            $table->timestamp('due_date')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();
        });

        // Table des commentaires sur les tickets
        Schema::create('ticket_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('tickets')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users');
            $table->text('content');
            $table->boolean('is_internal')->default(false); // Pour les notes internes visibles uniquement par les admins
            $table->timestamps();
        });

        // Table des pièces jointes
        Schema::create('ticket_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('tickets')->onDelete('cascade');
            $table->foreignId('comment_id')->nullable()->constrained('ticket_comments')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users');
            $table->string('filename');
            $table->string('path');
            $table->string('mime_type');
            $table->integer('size');
            $table->timestamps();
        });

        // Table pour les étiquettes (tags)
        Schema::create('ticket_tags', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('color')->default('#6c757d');
            $table->timestamps();
        });

        // Table pivot pour la relation many-to-many entre tickets et tags
        Schema::create('ticket_tag', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('tickets')->onDelete('cascade');
            $table->foreignId('tag_id')->constrained('ticket_tags')->onDelete('cascade');
            $table->timestamps();
        });

        // Table pour l'historique des tickets
        Schema::create('ticket_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('tickets')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users');
            $table->string('field_name');
            $table->text('old_value')->nullable();
            $table->text('new_value')->nullable();
            $table->timestamps();
        });

        // Table pour les abonnements aux tickets
        Schema::create('ticket_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('tickets')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_subscriptions');
        Schema::dropIfExists('ticket_histories');
        Schema::dropIfExists('ticket_tag');
        Schema::dropIfExists('ticket_tags');
        Schema::dropIfExists('ticket_attachments');
        Schema::dropIfExists('ticket_comments');
        Schema::dropIfExists('tickets');
        Schema::dropIfExists('ticket_priorities');
        Schema::dropIfExists('ticket_categories');
        Schema::dropIfExists('ticket_statuses');
    }
};
