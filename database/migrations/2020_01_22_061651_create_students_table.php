<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStudentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('students', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string("s_name");
            $table->string("s_surname");
            $table->string("s_fullname");
            $table->string("school_no")->nullable();
            $table->string("s_phone")->nullable();
            $table->string("s_gsm")->nullable();
            $table->string('s_email')->unique();
            $table->timestamp("s_birthday");
            $table->string("s_tc")->unique();
            $table->string("file_id")->nullable();
            $table->boolean("is_active")->default(true);
            $table->integer("s_gender")->default(0);
            $table->integer("s_family")->default(0);
            $table->string("s_address")->nullable();
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
        Schema::dropIfExists('students');
    }
}
