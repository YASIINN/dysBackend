<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClubDaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('club_days', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string("cdName");
            $table->unsignedBigInteger("club_p_type_id");
            $table->foreign('club_p_type_id')->references('id')->on('club_p_type')->onDelete("cascade")->onUpdate("cascade");
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
        Schema::dropIfExists('club_days');
    }
}
