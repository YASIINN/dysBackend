<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpecialNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('special_notes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger("to_user_id");
            $table->foreign('to_user_id')->references('id')->on('users')->onDelete("cascade")->onUpdate("cascade");
            $table->unsignedBigInteger("from_user_id");
            $table->foreign('from_user_id')->references('id')->on('users')->onDelete("cascade")->onUpdate("cascade");
            $table->text("content");
            $table->string("status", 2);
            $table->timestamp();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('special_notes');
    }
}
