<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClubPTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('club_p_type', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger("spor_club_id");
            $table->foreign('spor_club_id')->references('id')->on('spor_club')->onDelete("cascade")->onUpdate("cascade");
            $table->unsignedBigInteger("p_type_id");
            $table->foreign('p_type_id')->references('id')->on('p_types')->onDelete("cascade")->onUpdate("cascade");
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
        Schema::dropIfExists('club_p_type');
    }
}
