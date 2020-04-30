<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClubProgramContentUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('club_program_content_user', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger("user_id");
            $table->foreign('user_id')->references('id')->on('users')->onDelete("cascade")->onUpdate("cascade");
            $table->unsignedBigInteger("club_content_id");
            $table->foreign('club_content_id')->references('id')->on('club_program_contents')->onDelete("cascade")->onUpdate("cascade");
            $table->timestamps();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('club_program_content_user');
    }
}
