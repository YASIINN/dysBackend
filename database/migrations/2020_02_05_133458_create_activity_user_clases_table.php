<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActivityUserClasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activity_user_clases', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger("activity_id");
            $table->foreign('activity_id')->references('id')->on('activities')->onDelete("cascade")->onUpdate("cascade");
            $table->unsignedBigInteger("user_id");
            $table->foreign('user_id')->references('id')->on('users')->onDelete("cascade")->onUpdate("cascade");
            $table->unsignedBigInteger("grade_id");
            $table->foreign('grade_id')->references('id')->on('grades')->onDelete("cascade")->onUpdate("cascade");
            $table->unsignedBigInteger("period_id");
            $table->foreign('period_id')->references('id')->on('periods')->onDelete("cascade")->onUpdate("cascade");



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
        Schema::dropIfExists('activity_user_clases');
    }
}
