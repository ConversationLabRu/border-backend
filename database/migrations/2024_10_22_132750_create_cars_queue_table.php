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

        Schema::create('cars_queue', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('border_crossing_id')->unsigned();
            $table->foreign('border_crossing_id')->references('id')->on('borderсrossings');
            $table->timestamp('create_report_timestamp');
            $table->integer('count');
            $table->boolean('route_reverse');
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cars_queue');
    }
};
