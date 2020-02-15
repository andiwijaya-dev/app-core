<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBannerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('banner', function (Blueprint $table) {

          $table->bigIncrements('id');
          $table->timestamps();

          $table->smallInteger('type')->nullable();
          $table->boolean('is_active')->nullable();
          $table->dateTime('start_date')->nullable();
          $table->dateTime('end_date')->nullable();
          $table->string('title')->nullable();
          $table->string('image_url_desktop')->nullable();
          $table->string('image_url_mobile')->nullable();
          $table->string('target')->nullable();
          $table->smallInteger('sequence')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('banner');
    }
}
