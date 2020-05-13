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
          $table->string('module');
          $table->text('list');
          $table->text('create');
          $table->text('update');
          $table->text('delete');
          $table->text('import');
          $table->text('export');

          $table->foreign('user_id')->references('id')->on('user')
            ->onDelete('cascade')
            ->onUpdate('cascade');

          $table->unique([ 'user_id', 'module' ]);

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
