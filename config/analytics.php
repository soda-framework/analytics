<?php
    return [
        /*
         * The view id of which you want to display data.
         */
        'view_id'                          => env('ANALYTICS_VIEW_ID'),

        /*
         * Path to the client secret json file. Take a look at the README of this package
         * to learn how to get this file.
         */
        'service_account_credentials_json' => storage_path('app/analytics/service-account-credentials.json'),

        'client_secret' => storage_path('app/analytics/client-secret.json'),

        /*
         * The amount of minutes the Google API responses will be cached.
         * If you set this to zero, the responses won't be cached at all.
         */
        'cache_lifetime_in_minutes'        => 60 * 24,

        /*
         * Here you may configure the "store" that the underlying Google_Client will
         * use to store it's data.  You may also add extra parameters that will
         * be passed on setCacheConfig (see docs for google-api-php-client).
         *
         * Optional parameters: "lifetime", "prefix"
         */
        'cache'                            => [
            'store' => 'file',
        ],

        'auth' => [
            'provider' => [
                'driver' => 'eloquent',
                'model'  => Soda\Analytics\Database\Models\User::class,
            ],
            'guard'    => [
                'driver'   => 'session',
                'provider' => 'soda-analytics',
            ],
            'password' => [
                'provider' => 'soda-analytics',
                'email'    => 'auth.emails.password',
                'table'    => 'password_resets',
                'expire'   => 60,
            ],
        ],

        'apis' => [
            'analytics.googleapis.com',
            'analyticsreporting.googleapis.com',
            'iam.googleapis.com',
        ],
        'service-account-name' => 'Soda Analytics Service Account',
        'schedule' => [
            'frequencies' => [
                'daily',
                'weekly',
                'monthly',
                'quarterly',
                'yearly',
            ]
        ]
    ];
