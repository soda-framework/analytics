<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SodaAnalyticsUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('soda_analytics_users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->string('google_id',100)->nullable();
            $table->string('email')->nullable();
            $table->string('refresh_token')->nullable();
            $table->string('code')->nullable();
            $table->timestamps();
            $table->dateTime('last_loggedin_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('soda_analytics_users');
    }
}
