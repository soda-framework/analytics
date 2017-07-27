<?php
    return [
        'apis' => [
            'analytics.googleapis.com',
            'analyticsreporting.googleapis.com',
            'iam.googleapis.com',
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
        'scheduler' => [
            'frequencies' => [
                '0 0 0 1/1 * ? *'     => 'Daily',
                '0 0 0 ? * MON *'     => 'Weekly',
                '0 0 0 ? 1/1 MON#1 *' => 'Monthly',
                '0 0 0 ? 1/3 MON#1 *' => 'Quarterly',
                '0 0 0 1 1 ? *'       => 'Yearly',
            ]
        ],
        'service-account-name' => 'Soda Analytics Service Account'
    ];
