<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SodaAnalyticsAudience extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('soda_analytics_audience', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('users')->nullable();
            $table->integer('sessions')->nullable();
            $table->string('avg_session_duration',50)->nullable();
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
        Schema::dropIfExists('soda_analytics_audience');
    }
}
