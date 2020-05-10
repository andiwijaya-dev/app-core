<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserNotificationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_notification', function (Blueprint $table) {

          $table->bigIncrements('id');
          $table->timestamps();

          $table->boolean('status')->nullable();
          $table->string('title')->nullable();
          $table->string('body')->nullable();
          $table->string('target')->nullable();

          $table->bigInteger('user_id')->unsigned()->nullable();

          $table->foreign('user_id')->references('id')->on('user')
            ->onDelete('cascade')->onUpdate('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_notification');
    }
}
