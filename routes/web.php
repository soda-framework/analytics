<?php
    Route::group(['prefix' => config('soda.cms.path'), 'middleware' => 'soda.auth:soda'], function(){
        Route::group(['prefix' => 'analytics'], function(){

            Route::get('/', '\Soda\Analytics\Controllers\AnalyticsController@anyIndex')->name('soda.analytics');

            // AUTH
            Route::group(['prefix' => 'auth'], function () {
                Route::get('/', '\Soda\Analytics\Controllers\AuthController@redirectToProvider')->name('soda.analytics.auth');
                Route::get('/callback', '\Soda\Analytics\Controllers\AuthController@handleProviderCallback')->name('soda.analytics.auth.callback');
            });

            // CONFIGURE
            Route::group(['prefix' => 'configure'], function () {
                Route::get('/', '\Soda\Analytics\Controllers\ConfigureController@anyIndex')->name('soda.analytics.configure');
                Route::get('/enable-apis', '\Soda\Analytics\Controllers\ConfigureController@enableApis')->name('soda.analytics.configure.enable-apis');
                Route::get('/create-service-account', '\Soda\Analytics\Controllers\ConfigureController@createServiceAccount')->name('soda.analytics.configure.create-service-account');
                Route::get('/create-service-account-key', '\Soda\Analytics\Controllers\ConfigureController@createServiceAccountKey')->name('soda.analytics.configure.create-service-account-key');
                Route::get('/create-service-account-and-key', '\Soda\Analytics\Controllers\ConfigureController@createServiceAccountAndKey')->name('soda.analytics.configure.create-service-account-and-key');
                Route::get('/add-analytics-user', '\Soda\Analytics\Controllers\ConfigureController@addAnalyticsUser')->name('soda.analytics.configure.add-analytics-user');

                Route::post('/', '\Soda\Analytics\Controllers\ConfigureController@postConfigure')->name('soda.analytics.configure.post');
                Route::post('/accounts', '\Soda\Analytics\Controllers\AnalyticsController@postAccounts')->name('soda.analytics.configure.accounts');
                Route::post('/account-properties', '\Soda\Analytics\Controllers\AnalyticsController@postAccountProperties')->name('soda.analytics.configure.accounts');
                Route::post('/submit-account-property', '\Soda\Analytics\Controllers\AnalyticsController@postSubmitAccountProperty')->name('soda.analytics.configure.submit-account-property');
                Route::post('/create-account', '\Soda\Analytics\Controllers\AnalyticsController@postCreateAccount')->name('soda.analytics.configure.create-account');
                Route::post('/create-property', '\Soda\Analytics\Controllers\AnalyticsController@postCreateProperty')->name('soda.analytics.configure.create-property');
            });

            // EVENTS
            Route::group(['prefix' => 'events'], function () {
                Route::get('/', '\Soda\Analytics\Controllers\EventsController@anyIndex')->name('soda.analytics.events');
            });

            // AUDIENCE
            Route::group(['prefix' => 'audience'], function () {
                Route::get('/', '\Soda\Analytics\Controllers\AudienceController@anyIndex')->name('soda.analytics.audience');
            });

            // API


        });
    });
