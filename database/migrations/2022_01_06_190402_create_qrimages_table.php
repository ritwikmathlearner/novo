<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQrimagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('qrimages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('kol_session_id');
            $table->string('qr_code_image');
            $table->string('joining_url');
            $table->timestamps();

            $table->foreign('kol_session_id')->references('id')->on('kol_sessions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('qrimages');
    }
}
