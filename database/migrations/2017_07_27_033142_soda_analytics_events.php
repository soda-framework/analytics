<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SodaAnalyticsEvents extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('soda_analytics_events', function (Blueprint $table) {
            $table->increments('id');
            $table->string('category',100)->nullable();
            $table->string('action',100)->nullable();
            $table->string('label',100)->nullable();
            $table->integer('value')->nullable();
            $table->integer('total')->nullable();
            $table->integer('unique')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('soda_analytics_events');
    }
}
