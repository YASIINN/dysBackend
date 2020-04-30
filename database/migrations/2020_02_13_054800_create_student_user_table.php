<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStudentUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('student_user', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger("student_id");
            $table->unsignedBigInteger("users_id");
            $table->foreign('student_id')->references('id')->on('students')->onDelete("cascade")->onUpdate("cascade");
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
        Schema::dropIfExists('student_user');
    }
}
