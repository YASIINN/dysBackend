<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserSporClubTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_spor_club', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger("spor_club_id");
            $table->foreign('spor_club_id')->references('id')->on('spor_club')->onDelete("cascade")->onUpdate("cascade");
            $table->unsignedBigInteger("users_id");
            $table->foreign('users_id')->references('id')->on('users')->onDelete("cascade")->onUpdate("cascade");
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
        Schema::dropIfExists('user_spor_club');
    }
}
