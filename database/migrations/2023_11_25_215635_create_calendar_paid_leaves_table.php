<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('calendar_paid_leaves', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('paid_leave_id');
            $table->unsignedBigInteger('calendar_id');

            $table->foreign('paid_leave_id')->references('id')->on('paid_leaves')->onDelete('cascade');
            $table->foreign('calendar_id')->references('id')->on('calendars')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('calendar_paid_leaves');
    }
};
