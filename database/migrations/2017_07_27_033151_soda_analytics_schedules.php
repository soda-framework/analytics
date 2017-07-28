<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SodaAnalyticsSchedules extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('soda_analytics_schedules', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->string('type',50)->nullable();
            $table->string('emails')->nullable();
            $table->dateTime('analytics_from')->nullable();
            $table->dateTime('analytics_to')->nullable();
            $table->string('request')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('soda_analytics_schedules');
    }
}
