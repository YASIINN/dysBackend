<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommentFileTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comment_file', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger("comment_id");
            $table->foreign('comment_id')->references('id')->on('comment')->onDelete("cascade")->onUpdate("cascade");
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
        Schema::dropIfExists('comment_file');
    }
}
