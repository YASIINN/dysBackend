<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGradesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //bu tablo yaz okulu kış okulu gibi faliyetler için oluşturulacak sınıf gibi düşünülebilir.
        Schema::create('grades', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string("gName");
            $table->string("gCode");
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
        Schema::dropIfExists('grades');
    }
}
