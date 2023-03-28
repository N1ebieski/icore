<?php

/**
 * NOTICE OF LICENSE
 *
 * This source file is licenced under the Software License Agreement
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://intelekt.net.pl/pages/regulamin
 *
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * @author    Mariusz Wysokiński <kontakt@intelekt.net.pl>
 * @copyright Since 2019 INTELEKT - Usługi Komputerowe Mariusz Wysokiński
 * @license   https://intelekt.net.pl/pages/regulamin
 */

return [

    'version' => \N1ebieski\ICore\Providers\ICoreServiceProvider::VERSION,

    'license_key' => env('ICORE_LICENSE_KEY'),

    'theme' => env('ICORE_THEME', 'light'),

    'multi_themes' => explode(',', env('ICORE_MULTI_THEMES', 'light,dark')),

    'layout' => env('ICORE_LAYOUT', 'icore'),

    'multi_langs' => explode(',', env('ICORE_MULTI_LANGS', env('APP_LANG'))),

    'routes' => [
        'auth' => [
            'prefix' => env('ICORE_ROUTES_AUTH_PREFIX', null),
            'enabled' => true
        ],
        'web' => [
            'prefix' => env('ICORE_ROUTES_WEB_PREFIX', null),
            'enabled' => true
        ],
        'admin' => [
            'prefix' => env('ICORE_ROUTES_ADMIN_PREFIX', 'admin'),
            'enabled' => true
        ],
        'api' => [
            'prefix' => env('ICORE_ROUTES_API_PREFIX', 'api'),
            'enabled' => true
        ]
    ],

    'post' => [
        'max_categories' => (int)env('ICORE_POST_MAX_CATEGORIES', 5),
        'max_tags' => (int)env('ICORE_POST_MAX_TAGS', 10),
        'short_content' => (int)env('ICORE_POST_SHORT_CONTENT', 500),
    ],

    'page' => [
        'max_tags' => (int)env('ICORE_PAGE_MAX_TAGS', 10),
    ],

    'tag' => [
        'normalizer' => 'mb_strtolower',
        'max_chars' => (int)env('ICORE_TAG_MAX_CHARS', 30)
    ],

    'replacement' => [
        //
    ],

    'schedule' => [
        'resync' => env('ICORE_SCHEDULE_RESYNC', '00')
    ],

    'mailing' => [
        'delay' => (int)env('ICORE_MAILING_DELAY_MINUTES', 1),
        'limit' => (int)env('ICORE_MAILING_LIMIT', 100),
    ],

    'auto_translate' => [
        'check_days' => (int)env('ICORE_AUTO_TRANSLATE_CHECK_DAYS', 0),
    ],

    'captcha' => [
        'driver' => env('ICORE_CAPTCHA_DRIVER'),
    ],

    'home' => [
        'max' => (int)env('ICORE_HOME_MAX', 10)
    ],

    'sitemap' => [
        'limit' => (int)env('ICORE_SITEMAP_LIMIT', 1000),
        'max_items' => (int)env('ICORE_SITEMAP_MAX_ITEMS', 10000)
    ]
];
