<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTwoDNumberCombinationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('two_d_number_combinations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('two_d_numberset_id')->references('id')->on('two_d_number_sets')->onDelete('cascade');
            $table->string('visible_number',5)->nullable(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('two_d_number_combinations');
    }
}
