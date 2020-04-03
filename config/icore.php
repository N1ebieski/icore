<?php

return [

    'version' => \N1ebieski\ICore\Providers\ICoreServiceProvider::VERSION,

    'theme' => env('ICORE_THEME'),

    'layout' => env('ICORE_LAYOUT', 'icore'),

    'post' => [
        'max_categories' => (int)env('ICORE_POST_MAX_CATEGORIES', 5),
        'max_tags' => (int)env('ICORE_POST_MAX_TAGS', 10),
    ],

    'captcha' => [
        'driver' => env('ICORE_CAPTCHA_DRIVER'),
    ],

];
