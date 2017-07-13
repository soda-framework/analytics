<?php
    Route::group(['prefix' => config('soda.cms.path'), 'middleware' => 'soda.auth:soda'], function(){
        Route::group(['prefix' => 'analytics'], function(){

            Route::get('/', '\Soda\Analytics\Controllers\AnalyticsController@anyIndex')->name('soda.analytics');

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
