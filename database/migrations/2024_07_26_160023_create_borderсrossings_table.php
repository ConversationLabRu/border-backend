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
            $table->bigInteger('direction_id');
            $table->foreign('direction_id')->references('id')->on('directions');
            $table->text('from');
            $table->text('to');
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
