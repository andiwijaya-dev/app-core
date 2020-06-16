<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateScheduledTaskTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('scheduled_task', function (Blueprint $table) {

          $table->bigIncrements('id');

          $table->smallInteger('status'); // scheduled, running, completed, failed

          $table->string('creator')->nullable();
          $table->bigInteger('creator_id')->unsigned()->nullable();

          $table->string('description');
          $table->string('command');

          $table->dateTime('start')->nullable();

          $table->smallInteger('repeat'); // once, hourly, daily, weekly, monthly, every n-unit
          $table->text('repeat_options')->nullable();

          $table->string('flag', 1)->nullable();

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
        Schema::dropIfExists('scheduled_task');
    }
}
