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

namespace N1ebieski\ICore\Utils\Updater\Schema;

use N1ebieski\ICore\Utils\Updater\Schema\Interfaces\SchemaInterface;

class Schema820 implements SchemaInterface
{
    /**
     * Undocumented variable
     *
     * @var array
     */
    public $pattern = [
        [
            'paths' => [
                'resources/views/vendor/icore/mails/mailing.blade.php',
            ],
            'actions' => [
                [
                    'type' => 'replace',
                    'search' => '/\$mailingEmail->mailing->replacement_content_html/',
                    'to' => '$content'
                ]
            ]
        ],
        [
            'paths' => [
                'resources/views/vendor/icore/web/category/post/show.blade.php'
            ],
            'actions' => [
                [
                    'type' => 'beforeFirst',
                    'search' => '/<a[^>]*?href="{{\s*route\(\'web\.category\.post\.show\',\s*\[\$ancestor->slug\]\)\s*}}"[\s\S]*?>[\s\S]*?<\/a>/',
                    'to' => '@if ($ancestor->slug)'
                ],
                [
                    'type' => 'afterFirst',
                    'search' => '/<a[^>]*?href="{{\s*route\(\'web\.category\.post\.show\',\s*\[\$ancestor->slug\]\)\s*}}"[\s\S]*?>[\s\S]*?<\/a>/',
                    'to' => <<<EOD
    @else
        {{ trans('icore::multi_langs.no_trans') }}
    @endif
EOD
                ]
            ]
        ],
        [
            'paths' => [
                'resources/views/vendor/icore/web/page/show.blade.php'
            ],
            'actions' => [
                [
                    'type' => 'beforeFirst',
                    'search' => '/@if\s*\(\$ancestor->slug\)/',
                    'to' => '@if ($ancestor->slug)'
                ],
                [
                    'type' => 'afterFirst',
                    'search' => '/@else[\s\S]*?{{\s*\$ancestor->title\s*}}[\s\S]*?@endif/',
                    'to' => <<<EOD
    @else
        {{ trans('icore::multi_langs.no_trans') }}
    @endif
EOD
                ],
                [
                    'type' => 'beforeFirst',
                    'search' => '/<div[^>]*?class=".*">\s*<h1\s*class=".*">/',
                    'to' => '@includeWhen($page->currentLang->progress->isAutoTrans(), \'icore::web.partials.auto_trans\')'
                ]
            ]
        ],
        [
            'paths' => [
                'resources/views/vendor/icore/web/partials/nav.blade.php'
            ],
            'actions' => [
                [
                    'type' => 'replace',
                    'search' => '/href="\/"/',
                    'to' => 'href="{{ route(\'web.home.index\') }}"'
                ],
                [
                    'type' => 'beforeFirst',
                    'search' => '/@if\s*\(count\(config\(\'icore\.multi_themes\'\)\)\s*>\s*1\)/',
                    'to' => <<<EOD
                @if (count(config('icore.multi_langs')) > 1)
                <li class="nav-item dropdown">
                    <x-icore::multi-lang-component />
                </li>
                @endif
EOD
                ]
            ]
        ],
        [
            'paths' => [
                'resources/views/vendor/icore/web/post/show.blade.php'
            ],
            'actions' => [
                [
                    'type' => 'beforeFirst',
                    'search' => '/<div[^>]*?class=".*">\s*<h1\s*class=".*">/',
                    'to' => '@includeWhen($post->currentLang->progress->isAutoTrans(), \'icore::web.partials.auto_trans\')'
                ]
            ]
        ],
        [
            'paths' => [
                'resources/views/vendor/icore/web/profile/edit.blade.php'
            ],
            'actions' => [
                [
                    'type' => 'afterFirst',
                    'search' => '/@includeWhen\(\$errors->has\(\'name\'\),\s*\'icore::web\.partials\.errors\',\s*\[\'name\'\s*=>\s*\'name\'\]\)\s*<\/div>\s*<\/div>/',
                    'to' => <<<EOD
    @if (count(config('icore.multi_langs')) > 1)
    <div class="form-group row">
        <label for="pref_lang" class="col-lg-3 col-form-label text-lg-left">
            {{ trans('icore::profile.pref_lang') }}:
        </label>
        <div class="col-lg-6">
            <select 
                class="selectpicker select-picker" 
                data-style="border"
                data-width="100%"
                name="pref_lang"
                id="pref_lang"
            >
                @foreach (config('icore.multi_langs') as $lang)
                <option
                    data-content='<span class="fi fil-{{ $lang }}"></span> <span>{{ mb_strtoupper($lang) }}</span>'
                    value="{{ $lang }}"
                    {{ $user->pref_lang->getValue() === $lang ? 'selected' : '' }}
                >
                    {{ $lang }}
                </option>
                @endforeach
            </select>
            @includeWhen($errors->has('pref_lang'), 'icore::web.partials.errors', ['name' => 'pref_lang'])
        </div>
    </div>
    @endif 
EOD
                ]
            ]
        ]
    ];
}
