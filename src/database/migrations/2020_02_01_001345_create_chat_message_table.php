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

          $table->bigInteger('discussion_id')->unsigned();

          $table->smallInteger('unread');
          $table->boolean('direction');

          $table->string('text');
          $table->text('images')->nullable();
          $table->text('extra')->nullable();

          $table->foreign('discussion_id')->references('id')->on('chat_discussion')
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
