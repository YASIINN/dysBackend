<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('uName');
            $table->string("uSurname");
            $table->string("uJob")->nullable();
            $table->string("uPhone");
            $table->string("uPhoneOther")->nullable();
            $table->string("uEmail");
            $table->string("uTC", "11");
            $table->string("uAdress");
            $table->boolean("uİsActive");
            $table->tinyInteger("uGender");
            $table->tinyInteger("uEmailNotification");
            $table->tinyInteger("uSmsNotification");
            $table->date("uBirthDay")->nullable();
            $table->text("uFullName");

            $table->unsignedBigInteger("ufile_id");
            $table->foreign('ufile_id')->references('id')->on('files')->onDelete("cascade")->onUpdate("cascade");


            $table->unsignedBigInteger("uproximities_id");
            $table->foreign('uproximities_id')->references('id')->on('proximities')->onDelete("cascade")->onUpdate("cascade");

            $table->unsignedBigInteger("utitle_id");
            $table->foreign('utitle_id')->references('id')->on('titles')->onDelete("cascade")->onUpdate("cascade");

            $table->unsignedBigInteger("uprovince_id");
            $table->foreign('uprovince_id')->references('id')->on('provinces')->onDelete("cascade")->onUpdate("cascade");

            $table->unsignedBigInteger("uunits_id");
            $table->foreign('uunits_id')->references('id')->on('units')->onDelete("cascade")->onUpdate("cascade");


            $table->softDeletes();
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
        Schema::dropIfExists('users');
    }
}
