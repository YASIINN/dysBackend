<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActivityProgramTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activity_program', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger("activity_p_type_id");
            $table->foreign('activity_p_type_id')->references('id')->on('activity_p_type')->onDelete("cascade")->onUpdate("cascade");
            $table->unsignedBigInteger("grade_id");
            $table->foreign('grade_id')->references('id')->on('grades')->onDelete("cascade")->onUpdate("cascade");
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
        Schema::dropIfExists('activity_program');
    }
}
