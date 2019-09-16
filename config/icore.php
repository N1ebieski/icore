<?php

return [

    'license_key' => env('ICORE_LICENSE_KEY'),

    'theme' => env('ICORE_THEME'),

    'layout' => 'icore',

    'post' => [
        'max_categories' => env('ICORE_BLOG_MAX_CATEGORIES', 5),
        'max_tags' => env('ICORE_BLOG_MAX_TAGS', 10),
    ],

    'captcha' => [
        'driver' => env('ICORE_CAPTCHA_DRIVER'),
    ],

];
