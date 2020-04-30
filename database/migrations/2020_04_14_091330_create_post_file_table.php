<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePostFileTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('post_file', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger("post_id");
            $table->foreign('post_id')->references('id')->on('post')->onDelete("cascade")->onUpdate("cascade");
            $table->unsignedBigInteger("files_id");
            $table->foreign('files_id')->references('id')->on('files')->onDelete("cascade")->onUpdate("cascade");
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
        Schema::dropIfExists('post_file');
    }
}
