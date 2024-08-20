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
        Schema::disableForeignKeyConstraints();

        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('border_crossing_id')->unsigned();
            $table->foreign('border_crossing_id')->references('id')->on('borderсrossings');
            $table->bigInteger('transport_id')->unsigned();
            $table->foreign('transport_id')->references('id')->on('transports');
            $table->bigInteger('user_id')->unsigned();
            $table->bigInteger('user_id');
            $table->timestamp('checkpoint_queue')->nullable();
            $table->timestamp('checkpoint_entry');
            $table->timestamp('checkpoint_exit');
            $table->text('comment')->nullable();
            $table->boolean('is_flipped_direction');
            $table->timestamp('time_enter_waiting_area')->nullable();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
