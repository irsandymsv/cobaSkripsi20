<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHsImageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hs_image', function (Blueprint $table) {
            $table->increments('id');
            $table->text('image');
            $table->smallInteger('width');
            $table->smallInteger('height');
            $table->tinyInteger('peak');
            $table->tinyInteger('zero');
            $table->mediumInteger('kapasitas');
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
        Schema::dropIfExists('hs_image');
    }
}
