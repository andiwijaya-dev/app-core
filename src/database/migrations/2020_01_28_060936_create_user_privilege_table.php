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

          $table->bigInteger('user_id')->unsigned()->nullable();
          $table->bigInteger('module_id')->unsigned()->nullable();
          $table->boolean('list');
          $table->boolean('create');
          $table->boolean('update');
          $table->boolean('delete');
          $table->boolean('import');
          $table->boolean('export');

          $table->foreign('user_id')->references('id')->on('user')
            ->onDelete('cascade')
            ->onUpdate('cascade');

          $table->unique([ 'user_id', 'module_id' ]);

          $table->index([ 'user_id' ]);

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
