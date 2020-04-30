<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActivityPTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activity_p_type', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger("activity_id");
            $table->foreign('activity_id')->references('id')->on('activities')->onDelete("cascade")->onUpdate("cascade");
            $table->unsignedBigInteger("period_id");
            $table->foreign('period_id')->references('id')->on('periods')->onDelete("cascade")->onUpdate("cascade");
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
        Schema::dropIfExists('activity_p_type');
    }
}
