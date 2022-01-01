<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFeedbackTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('feedback', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('kol_session_id');
            $table->unsignedBigInteger('attendee_id');
            $table->integer('feedback');
            $table->timestamps();

            $table->foreign('attendee_id')->references('id')->on('attendees');
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
        Schema::dropIfExists('feedback');
    }
}
