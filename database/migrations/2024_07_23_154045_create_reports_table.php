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
            $table->bigInteger('border_crossing_id');
            $table->foreign('border_crossing_id')->references('id')->on('border_сrossings');
            $table->bigInteger('transport_id');
            $table->foreign('transport_id')->references('id')->on('transports');
            $table->dateTime('checkpoint_queue')->nullable();
            $table->dateTime('checkpoint_entry');
            $table->dateTime('checkpoint_exit');
            $table->text('сomment')->nullable();
            $table->bigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
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
