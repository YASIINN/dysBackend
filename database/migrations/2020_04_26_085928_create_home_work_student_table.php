<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHomeWorkStudentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('home_work_student', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger("post_id");
            $table->foreign('post_id')->references('id')->on('post')->onDelete("cascade")->onUpdate("cascade");

            $table->unsignedBigInteger("home_work_cat_id");
            $table->foreign('home_work_cat_id')->references('id')->on('home_work_category')->onDelete("cascade")->onUpdate("cascade");
            $table->unsignedBigInteger("student_id");
            $table->foreign('student_id')->references('id')->on('students')->onDelete("cascade")->onUpdate("cascade");
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
        Schema::dropIfExists('home_work_student');
    }
}
