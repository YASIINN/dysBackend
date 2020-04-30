<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSchoolHoursTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('school_hours', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string("shName");
            $table->string("beginDate");
            $table->string("endDate");
            $table->unsignedBigInteger("school_p_type_id");
            $table->foreign('school_p_type_id')->references('id')->on('school_p_type')->onDelete("cascade")->onUpdate("cascade");
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
        Schema::dropIfExists('school_hours');
    }
}
