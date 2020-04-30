<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSporClubTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('spor_club', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string("scName");
            $table->string("scCode");
            $table->unsignedBigInteger("company_id");
            $table->foreign('company_id')->references('id')->on('companies')->onDelete("cascade")->onUpdate("cascade");
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
        Schema::dropIfExists('spor_club');
    }
}
