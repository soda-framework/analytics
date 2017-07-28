<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SodaAnalyticsConfig extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('soda_analytics_config', function (Blueprint $table) {
            $table->increments('id');
            $table->string('project_id')->nullable();
            $table->string('client_id')->nullable();
            $table->string('client_secret')->nullable();
            $table->boolean('apis_enabled')->nullable();
            $table->text('service_account_credentials_json')->nullable();
            $table->string('account_id',100)->nullable();
            $table->string('account_name')->nullable();
            $table->string('property_id',100)->nullable();
            $table->string('property_name')->nullable();
            $table->string('view_id')->nullable();
            $table->string('view_name')->nullable();
            $table->boolean('analytics_user_added')->nullable();
            $table->dateTime('analytics_from')->nullable();
            $table->dateTime('analytics_to')->nullable();
            $table->string('schedule_frequency',100)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('soda_analytics_config');
    }
}
