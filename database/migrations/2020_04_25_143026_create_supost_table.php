<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSupostTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('supost', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger("contentable_id");
            $table->string("contentable_type");
            $table->unsignedBigInteger("post_id");
            $table->foreign('post_id')->references('id')->on('post')->onDelete("cascade")->onUpdate("cascade");
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
        Schema::dropIfExists('supost');
    }
}
