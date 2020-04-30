<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserSporClubTeamBranchTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_spor_club_team_branch', function (Blueprint $table) {
            $table->bigIncrements('usctbid');
            $table->unsignedBigInteger("spor_club_id");
            $table->foreign('spor_club_id')->references('id')->on('spor_club')->onDelete("cascade")->onUpdate("cascade");
            $table->unsignedBigInteger("user_id");
            $table->foreign('user_id')->references('id')->on('users')->onDelete("cascade")->onUpdate("cascade");
            $table->unsignedBigInteger("team_id");
            $table->foreign('team_id')->references('id')->on('team')->onDelete("cascade")->onUpdate("cascade");
            $table->unsignedBigInteger("spor_club_team_branch_id");
            $table->foreign('spor_club_team_branch_id')->references('id')->on('spor_club_branch')->onDelete("cascade")->onUpdate("cascade");
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
        Schema::dropIfExists('user_spor_club_team_branch');
    }
}
