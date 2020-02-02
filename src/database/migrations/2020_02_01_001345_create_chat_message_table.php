<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChatMessageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chat_message', function (Blueprint $table) {

          $table->bigIncrements('id');
          $table->timestamps();

          $table->bigInteger('chat_id')->unsigned();

          $table->smallInteger('unread');
          $table->boolean('direction');

          $table->bigInteger('from_id')->nullable();
          $table->bigInteger('to_id')->nullable();

          $table->text('topic')->nullable();
          $table->string('message')->default('');
          $table->text('extra')->nullable();

          $table->foreign('chat_id')->references('id')->on('chat')
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
        Schema::dropIfExists('chat_message');
    }
}
