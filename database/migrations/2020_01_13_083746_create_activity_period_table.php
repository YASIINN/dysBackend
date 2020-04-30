<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActivityPeriodTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activity_period', function (Blueprint $table) {
            $table->engine = "MyISAM";
            $table->bigIncrements('id');
            $table->integer("activity_id")->unsigned()->nullable()->index();
            $table->integer("period_id")->unsigned()->nullable()->index();
            $table->integer("grade_id")->unsigned()->nullable()->index();
            // $table->integer("lesson_id")->unsigned()->nullable()->index();
            // $table->integer("student_id")->unsigned()->nullable()->index();
            $table->timestamp("begin")->nullable();
            $table->timestamp("end")->nullable();
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
        Schema::dropIfExists('activity_period');
    }
}
