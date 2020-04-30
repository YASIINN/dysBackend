<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClubTeamBranchStudentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('club_team_branch_student', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger("spor_club_id");
            $table->unsignedBigInteger("team_id");
            $table->unsignedBigInteger("spor_club_branch_id");
            $table->unsignedBigInteger("student_id");
            $table->foreign('student_id')->references('id')->on('students')->onDelete("cascade")->onUpdate("cascade");
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
        Schema::dropIfExists('club_team_branch_student');
    }
}
