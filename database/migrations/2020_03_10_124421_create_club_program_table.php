<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClubProgramTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('club_program', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger("club_p_type_id");
            $table->foreign('club_p_type_id')->references('id')->on('club_p_type')->onDelete("cascade")->onUpdate("cascade");

            $table->unsignedBigInteger("spor_club_id");
            $table->unsignedBigInteger("team_id");
            $table->unsignedBigInteger("spor_club_branch_id");

            $table->foreign('team_id')->references('id')->on('team')->onDelete("cascade")->onUpdate("cascade");
            $table->foreign('spor_club_id')->references('id')->on('spor_club')->onDelete("cascade")->onUpdate("cascade");
            $table->foreign('spor_club_branch_id')->references('id')->on('spor_club_branch')->onDelete("cascade")->onUpdate("cascade");
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
        Schema::dropIfExists('club_program');
    }
}
