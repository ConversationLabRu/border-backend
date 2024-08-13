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

        Schema::create('cameras', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('border_crossing_id')->unsigned();
            $table->foreign('border_crossing_id')->references('id')->on('borderсrossings');
            $table->text('url');
            $table->text('description');
            $table->text('photo');
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cameras');
    }
};
