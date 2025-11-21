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
        Schema::table('plannings', function (Blueprint $table) {
            $table->index(['user_id', 'calendar_id'], 'plannings_user_calendar_index');
            $table->index(['team_id', 'calendar_id'], 'plannings_team_calendar_index');
            $table->index('type_day', 'plannings_type_day_index');
            $table->index('created_at', 'plannings_created_at_index');
        });

        Schema::table('calendars', function (Blueprint $table) {
            $table->index('date', 'calendars_date_index');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->index('team_id', 'users_team_id_index');
            $table->index('account_active', 'users_account_active_index');
        });

        Schema::table('exchange_requests', function (Blueprint $table) {
            $table->index(['requester_id', 'status'], 'exchange_requests_requester_status_index');
            $table->index(['requested_id', 'status'], 'exchange_requests_requested_status_index');
            $table->index('team_id', 'exchange_requests_team_id_index');
        });

        Schema::table('tickets', function (Blueprint $table) {
            $table->index(['team_id', 'status_id'], 'tickets_team_status_index');
            $table->index(['assigned_to', 'status_id'], 'tickets_assigned_status_index');
            $table->index('created_at', 'tickets_created_at_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plannings', function (Blueprint $table) {
            $table->dropIndex('plannings_user_calendar_index');
            $table->dropIndex('plannings_team_calendar_index');
            $table->dropIndex('plannings_type_day_index');
            $table->dropIndex('plannings_created_at_index');
        });

        Schema::table('calendars', function (Blueprint $table) {
            $table->dropIndex('calendars_date_index');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_team_id_index');
            $table->dropIndex('users_account_active_index');
        });

        Schema::table('exchange_requests', function (Blueprint $table) {
            $table->dropIndex('exchange_requests_requester_status_index');
            $table->dropIndex('exchange_requests_requested_status_index');
            $table->dropIndex('exchange_requests_team_id_index');
        });

        Schema::table('tickets', function (Blueprint $table) {
            $table->dropIndex('tickets_team_status_index');
            $table->dropIndex('tickets_assigned_status_index');
            $table->dropIndex('tickets_created_at_index');
        });
    }
};
