<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserutypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_u_types', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger("users_id");
            $table->foreign('users_id')->references('id')->on('users')->onDelete("cascade")->onUpdate("cascade");
            $table->unsignedBigInteger("user_types_id");
            $table->foreign('user_types_id')->references('id')->on('users_types')->onDelete("cascade")->onUpdate("cascade");
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
        Schema::dropIfExists('user_u_types');
    }
}
