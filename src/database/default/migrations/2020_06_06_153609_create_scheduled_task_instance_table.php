<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateScheduledTaskInstanceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('scheduled_task_instance', function (Blueprint $table) {

          $table->bigIncrements('id');

          $table->bigInteger('task_id')->unsigned();

          $table->smallInteger('status'); // scheduled, running, completed, failed
          $table->string('command');

          $table->dateTime('start')->nullable();

          $table->smallInteger('result')->nullable();
          $table->text('result_details')->nullable();
          $table->dateTime('completed_at')->nullable();
          $table->double('ellapsed', 13, 3)->nullable();

          $table->timestamps();

          $table->integer('pid')->nullable();

          $table->foreign('task_id')->references('id')->on('scheduled_task')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('scheduled_task_instance');
    }
}
