<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CrateSchoolLessonsPivotsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('school_lessons_pivots', function (Blueprint $table) {
            $table->bigIncrements('slid');
            $table->unsignedBigInteger("school_id");
            $table->foreign('school_id')->references('id')->on('schools')->onDelete("cascade")->onUpdate("cascade");
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
        Schema::dropIfExists('school_lessons_pivots');
    }
}
