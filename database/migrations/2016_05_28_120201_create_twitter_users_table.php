<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTwitterUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('twitter_users', function (Blueprint $table) {
            $table->increments('id');
            $table->text('oauth_token');
            $table->text('oauth_token_secret');
            $table->bigInteger('user_id');
            $table->string('screen_name');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('twitter_users');
    }
}
