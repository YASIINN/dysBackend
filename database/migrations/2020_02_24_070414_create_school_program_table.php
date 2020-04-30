<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSchoolProgramTable extends Migration
{
    
    public function up()
    {
        Schema::create('school_program', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger("school_p_type_id");
            $table->foreign('school_p_type_id')->references('id')->on('school_p_type')->onDelete("cascade")->onUpdate("cascade");
            $table->unsignedBigInteger("school_id");
            $table->foreign('school_id')->references('id')->on('schools')->onDelete("cascade")->onUpdate("cascade");
            $table->unsignedBigInteger("clases_id");
            $table->foreign('clases_id')->references('id')->on('clases')->onDelete("cascade")->onUpdate("cascade");
            $table->unsignedBigInteger("branches_id");
            $table->foreign('branches_id')->references('id')->on('branches')->onDelete("cascade")->onUpdate("cascade");
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
        Schema::dropIfExists('school_program');
    }
}
