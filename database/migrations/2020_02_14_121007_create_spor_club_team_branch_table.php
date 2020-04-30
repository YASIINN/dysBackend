<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSporClubTeamBranchTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('spor_club_team_branch', function (Blueprint $table) {
            $table->bigIncrements('sctbid');
            $table->unsignedBigInteger("spor_club_id");
            $table->foreign('spor_club_id')->references('id')->on('spor_club')->onDelete("cascade")->onUpdate("cascade");
            $table->unsignedBigInteger("team_id");
            $table->foreign('team_id')->references('id')->on('team')->onDelete("cascade")->onUpdate("cascade");
            $table->unsignedBigInteger("sbranch_id");
            $table->foreign('sbranch_id')->references('id')->on('spor_club_branch')->onDelete("cascade")->onUpdate("cascade");
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
        Schema::dropIfExists('spor_club_team_branch');
    }
}
