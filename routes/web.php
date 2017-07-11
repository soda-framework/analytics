<?php
    Route::group(['prefix' => config('soda.cms.path'), 'middleware' => 'soda.auth:soda'], function(){
        Route::group(['prefix' => 'analytics'], function(){

            Route::get('/', '\Soda\Analytics\Controllers\AnalyticsController@anyIndex')->name('analytics');

            // AUTH
            Route::group(['prefix' => 'auth'], function () {
                Route::get('/', '\Soda\Analytics\Controllers\AuthController@redirectToProvider')->name('analytics.auth');
                Route::get('/callback', '\Soda\Analytics\Controllers\AuthController@handleProviderCallback')->name('analytics.auth.callback');
            });

            // API
            Route::post('/accounts', '\Soda\Analytics\Controllers\AnalyticsController@postAccounts')->name('api.analytics.accounts');
            Route::post('/account-properties', '\Soda\Analytics\Controllers\AnalyticsController@postAccountProperties')->name('api.analytics.accounts');
            Route::post('/submit-account-property', '\Soda\Analytics\Controllers\AnalyticsController@postSubmitAccountProperty')->name('api.analytics.submit-account-property');
            Route::post('/create-account', '\Soda\Analytics\Controllers\AnalyticsController@postCreateAccount')->name('api.analytics.create-account');
            Route::post('/create-property', '\Soda\Analytics\Controllers\AnalyticsController@postCreateProperty')->name('api.analytics.create-property');

        });
    });
