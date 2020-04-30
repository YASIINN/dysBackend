<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActivityGradePeriodTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activity_grade_period', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer("activity_id")->unsigned()->nullable()->index();
            $table->integer("period_id")->unsigned()->nullable()->index();
            $table->integer("grade_id")->unsigned()->nullable()->index();
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
        Schema::dropIfExists('activity_grade_period');
    }
}
