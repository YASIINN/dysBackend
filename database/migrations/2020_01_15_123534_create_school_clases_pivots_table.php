<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSchoolClasesPivotsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('school_clases_pivots', function (Blueprint $table) {
            $table->bigIncrements('scid');
            $table->unsignedBigInteger("school_id");
            $table->foreign('school_id')->references('id')->on('schools')->onDelete("cascade")->onUpdate("cascade");
            $table->unsignedBigInteger("clases_id");
            $table->foreign('clases_id')->references('id')->on('clases')->onDelete("cascade")->onUpdate("cascade");
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
        Schema::dropIfExists('school_clases_pivots');
    }
}
