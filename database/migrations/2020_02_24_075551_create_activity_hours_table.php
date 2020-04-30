<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActivityHoursTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activity_hours', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string("ahName");
            $table->string("beginDate");
            $table->string("endDate");
            $table->unsignedBigInteger("activity_p_type_id");
            $table->foreign('activity_p_type_id')->references('id')->on('activity_p_type')->onDelete("cascade")->onUpdate("cascade");
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
        Schema::dropIfExists('activity_hours');
    }
}
