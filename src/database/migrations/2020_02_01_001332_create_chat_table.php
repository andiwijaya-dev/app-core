<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChatTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chat', function (Blueprint $table) {

          $table->bigIncrements('id');
          $table->timestamps();

          $table->string('title', 100)->unique();
          $table->string('image_url');
          $table->text('extra')->nullable();

          $table->boolean('status');
          $table->smallInteger('unread_count')->default(0);

          $table->timestamp('last_message_at')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('chat');
    }
}
