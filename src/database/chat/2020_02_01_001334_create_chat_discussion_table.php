<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChatDiscussionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chat_discussion', function (Blueprint $table) {

          $table->bigIncrements('id');
          $table->timestamps();

          $table->boolean('status');
          $table->string('key');
          $table->string('name');
          $table->string('title');
          $table->string('avatar_image_url')->nullable();
          $table->text('extra')->nullable();
          $table->smallInteger('unreplied_count')->nullable()->default(0);
          $table->timestamp('last_replied_at')->nullable();

          $table->unique('key', 'chat-discussion-key-exists');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('chat_discussion');
    }
}
