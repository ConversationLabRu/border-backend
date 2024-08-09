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

        Schema::create('borderсrossings', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('direction_id')->unsigned();
            $table->foreign('direction_id')->references('id')->on('directions');
            $table->bigInteger('from_id')->unsigned();
            $table->foreign('from_id')->references('id')->on('cities');
            $table->bigInteger('to_id')->unsigned();
            $table->foreign('to_id')->references('id')->on('cities');
            $table->boolean('is_quque');
            $table->text('header_image');
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('borderсrossings');
    }
};
