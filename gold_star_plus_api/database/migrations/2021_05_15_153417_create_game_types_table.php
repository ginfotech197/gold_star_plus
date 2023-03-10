<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGameTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('game_types', function (Blueprint $table) {
            $table->id();
            $table->string('game_type_name',20)->nullable(true);
            $table->string('game_name',20)->nullable(true);
            $table->string('series_name',2)->nullable(true);
            $table->string('game_type_initial',10)->nullable(true);
            $table->decimal('mrp',5,2)->default(0);
            $table->decimal('winning_price',10,2)->default(0);
            $table->decimal('winning_bonus_percent',10,2)->default(0);
            $table->decimal('commission',10,2)->default(0);
            $table->decimal('payout',10,2)->default(0);
            $table->decimal('default_payout',10,2)->default(0);
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
        Schema::dropIfExists('game_types');
    }
}
