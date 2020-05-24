<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserPresetTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_preset', function (Blueprint $table) {

          $table->bigIncrements('id');
          $table->timestamps();

          $table->bigInteger('user_id')->unsigned();
          $table->string('key');
          $table->text('value');

          $table->index([ 'user_id', 'key' ]);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_preset');
    }
}
