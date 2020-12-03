<?php

return [

    'version' => \N1ebieski\ICore\Providers\ICoreServiceProvider::VERSION,

    'theme' => env('ICORE_THEME'),

    'layout' => env('ICORE_LAYOUT', 'icore'),

    'post' => [
        'max_categories' => (int)env('ICORE_POST_MAX_CATEGORIES', 5),
        'max_tags' => (int)env('ICORE_POST_MAX_TAGS', 10),
        'short_content' => (int)env('ICORE_POST_SHORT_CONTENT', 500),
    ],

    'page' => [
        'max_tags' => (int)env('ICORE_PAGE_MAX_TAGS', 10),
    ],

    'tag' => [
        'normalizer' => 'mb_strtolower'
    ],

    'replacement' => [
        //
    ],

    'mailing' => [
        'delay' => (int)env('ICORE_MAILING_DELAY_MINUTES', 1),
        'limit' => (int)env('ICORE_MAILING_LIMIT', 100),
    ],

    'captcha' => [
        'driver' => env('ICORE_CAPTCHA_DRIVER'),
    ],
];
