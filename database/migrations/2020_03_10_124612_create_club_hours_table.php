<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClubHoursTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('club_hours', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string("chName");
            $table->string("beginDate");
            $table->string("endDate");
            $table->unsignedBigInteger("club_p_type_id");
            $table->foreign('club_p_type_id')->references('id')->on('club_p_type')->onDelete("cascade")->onUpdate("cascade");
            $table->timestamps();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('club_hours');
    }
}
