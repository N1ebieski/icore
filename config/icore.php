<?php

return [
    'app' => [
        'desc' => env('ICORE_APP_DESC'),
        'keys' => env('ICORE_APP_KEYS'),
        'theme' => env('ICORE_APP_THEME'),
        'license_key' => env('ICORE_LICENSE_KEY'),
        'locale_full' => env('ICORE_LANG_FULL', 'pl_PL.UTF-8'),
    ],

    'auth' => [
        'providers' => [
            'users' => [
                'model' => \N1ebieski\ICore\Models\User::class,
            ]
        ]
    ],

    'post' => [
        'max_categories' => env('ICORE_BLOG_MAX_CATEGORIES', 5),
        'max_tags' => env('ICORE_BLOG_MAX_TAGS', 10),
    ],

    'cache' => [
        'minutes' => env('ICORE_CACHE_MINUTES', 15)
    ],

    'captcha' => [
        'driver' => env('ICORE_CAPTCHA_DRIVER'),

        'configs' => [
            'recaptcha_v2' => [
                'secret_key' => env('RECAPTCHA_V2_SECRET_KEY'),
                'site_key'   => env('RECAPTCHA_V2_SITE_KEY'),
            ]
        ]
    ],

    'database' => [
        'connections' => [
            'mysql' => [
                'collation' => 'utf8mb4_polish_ci'
            ]
        ],
        'paginate' => env('ICORE_DB_PAGINATE', 25),
    ],


];
