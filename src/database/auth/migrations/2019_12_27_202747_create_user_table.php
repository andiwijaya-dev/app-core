<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user', function (Blueprint $table) {

          $table->bigIncrements('id');

          $table->bigInteger('is_active')->default(0);
          $table->bigInteger('is_admin')->default(0);

          $table->string('code')->nullable();
          $table->string('name');
          $table->string('email');

          $table->string('avatar_url')->nullable();

          $table->string('password')->nullable();
          $table->boolean('require_password_change')->nullable();

          $table->string('referral_code')->nullable();
          $table->bigInteger('referral_id')->unsigned()->nullable();

          $table->rememberToken();

          $table->timestamp('last_login_at')->nullable();
          $table->timestamp('email_verified_at')->nullable();
          $table->timestamps();

          $table->unique('code', 'user-code-unique');
          $table->unique('email', 'user-email-unique');

          $table->foreign('referral_id')->references('id')->on('user')->onDelete('restrict')->onUpdate('restrict');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user');
    }
}
