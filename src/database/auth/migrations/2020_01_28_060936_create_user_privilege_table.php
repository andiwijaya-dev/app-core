<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserPrivilegeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_privilege', function (Blueprint $table) {

          $table->bigIncrements('id');
          $table->timestamps();

          $table->bigInteger('user_id')->unsigned();
          $table->string('key');
          $table->text('value');

          $table->foreign('user_id')->references('id')->on('user')
            ->onDelete('cascade')
            ->onUpdate('cascade');

          $table->unique([ 'user_id', 'key' ], 'user_privilege_unq1');

          $table->index([ 'user_id' ], 'user_privilege_idx1');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_privilege');
    }
}
