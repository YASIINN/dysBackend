<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePostTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('post', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->longText("content");
            $table->string("sharedtype");
            $table->date("delivery_date")->nullable();
            $table->string("estimated_time")->nullable();
            $table->tinyInteger("iscomment");
            $table->string("exam_name")->nullable();
            $table->string("hour")->nullable();
            $table->string("isstatus")->nullable();

            $table->unsignedBigInteger("user_id");
            $table->foreign('user_id')->references('id')->on('users')->onDelete("cascade")->onUpdate("cascade");

            $table->unsignedBigInteger("category_id");
            $table->foreign('category_id')->references('id')->on('home_work_type')->onDelete("cascade")->onUpdate("cascade");

            $table->unsignedBigInteger("post_type_id");
            $table->foreign('post_type_id')->references('id')->on('post_type')->onDelete("cascade")->onUpdate("cascade");

            $table->unsignedBigInteger("tag_id");
            $table->foreign('tag_id')->references('id')->on('post_tag')->onDelete("cascade")->onUpdate("cascade");

            $table->unsignedBigInteger("lesson_id");
            $table->foreign('lesson_id')->references('id')->on('lessons')->onDelete("cascade")->onUpdate("cascade");


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
        Schema::dropIfExists('post');
    }
}
