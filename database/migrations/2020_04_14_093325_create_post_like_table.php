<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePostLikeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('post_like', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger("post_id");
            $table->foreign('post_id')->references('id')->on('post')->onDelete("cascade")->onUpdate("cascade");
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
        Schema::dropIfExists('post_like');
    }
}
