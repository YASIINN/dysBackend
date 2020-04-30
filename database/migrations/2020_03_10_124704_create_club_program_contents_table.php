<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClubProgramContentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('club_program_contents', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger("club_program_id");
            $table->foreign('club_program_id')->references('id')->on('club_program')->onDelete("cascade")->onUpdate("cascade");
            $table->unsignedBigInteger("club_day_id");
            $table->foreign('club_day_id')->references('id')->on('club_days')->onDelete("cascade")->onUpdate("cascade");
            $table->unsignedBigInteger("club_hour_id");
            $table->foreign('club_hour_id')->references('id')->on('club_hours')->onDelete("cascade")->onUpdate("cascade");
            $table->string("description");
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
        Schema::dropIfExists('club_program_contents');
    }
}
