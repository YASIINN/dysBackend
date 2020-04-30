<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDiscontinuitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('discontinuities', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger("contentable_id");
            $table->string("contentable_type");
            $table->date("discontDate");
            $table->unsignedBigInteger("d_type_id");
            $table->foreign('d_type_id')->references('id')->on('d_types')->onDelete("cascade")->onUpdate("cascade");
            $table->unsignedBigInteger("student_id");
            $table->foreign('student_id')->references('id')->on('students')->onDelete("cascade")->onUpdate("cascade");
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
        Schema::dropIfExists('discontinuities');
    }
}
