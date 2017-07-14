<?php
    Route::group(['prefix' => config('soda.cms.path'), 'middleware' => 'soda.auth:soda'], function(){
        Route::group(['prefix' => 'analytics'], function(){

            Route::get('/', '\Soda\Analytics\Controllers\AnalyticsController@anyIndex')->name('soda.analytics');

            // CONFIGURE
            Route::group(['prefix' => 'configure'], function () {
                Route::get('/step-1', '\Soda\Analytics\Controllers\ConfigureController@anyStep1')->name('soda.analytics.configure.step-1');
                Route::get('/step-2', '\Soda\Analytics\Controllers\ConfigureController@anyStep2')->name('soda.analytics.configure.step-2');
                Route::post('/', '\Soda\Analytics\Controllers\ConfigureController@postConfigure')->name('soda.analytics.configure.post');
            });

            // AUTH
            Route::group(['prefix' => 'auth'], function () {
                Route::get('/', '\Soda\Analytics\Controllers\AuthController@redirectToProvider')->name('soda.analytics.auth');
                Route::get('/callback', '\Soda\Analytics\Controllers\AuthController@handleProviderCallback')->name('soda.analytics.auth.callback');
            });

            // API
            Route::post('/accounts', '\Soda\Analytics\Controllers\AnalyticsController@postAccounts')->name('soda.analytics.accounts');
            Route::post('/account-properties', '\Soda\Analytics\Controllers\AnalyticsController@postAccountProperties')->name('soda.analytics.accounts');
            Route::post('/submit-account-property', '\Soda\Analytics\Controllers\AnalyticsController@postSubmitAccountProperty')->name('soda.analytics.submit-account-property');
            Route::post('/create-account', '\Soda\Analytics\Controllers\AnalyticsController@postCreateAccount')->name('soda.analytics.create-account');
            Route::post('/create-property', '\Soda\Analytics\Controllers\AnalyticsController@postCreateProperty')->name('soda.analytics.create-property');

        });
    });
