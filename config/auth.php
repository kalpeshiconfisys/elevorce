<?php

return [

    'defaults' => [
        'guard' => 'web',
        'passwords' => 'users',
    ],

    'guards' => [

        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],

        'admin' => [
            'driver' => 'session',
            'provider' => 'admins',
        ],

        'employer' => [
            'driver' => 'jwt',
            'provider' => 'users',
        ],

        'employee' => [
            'driver' => 'jwt',
            'provider' => 'employees',
        ],

        'site-manager' => [
            'driver' => 'jwt',
            'provider' => 'site_managers',
        ],
    ],

    'providers' => [

        'admins' => [
            'driver' => 'eloquent',
            'model' => App\Models\Admin::class,
        ],

        'users' => [
            'driver' => 'eloquent',
            'model' => App\Models\User::class,
        ],

        'employees' => [
            'driver' => 'eloquent',
            'model' => App\Models\Employee::class,
        ],

        'site_managers' => [
            'driver' => 'eloquent',
            'model' => App\Models\SiteManager::class,
        ],

        // If you're using DB instead of Eloquent:
        // 'employees' => [
        //     'driver' => 'database',
        //     'table' => 'employees',
        // ],
    ],

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => 'password_resets',
            'expire' => 60,
            'throttle' => 60,
        ],

        // Optionally, separate reset settings per provider:
        // 'employees' => [
        //     'provider' => 'employees',
        //     'table' => 'employee_password_resets',
        //     'expire' => 60,
        //     'throttle' => 60,
        // ],
    ],

    'password_timeout' => 10800,

];
