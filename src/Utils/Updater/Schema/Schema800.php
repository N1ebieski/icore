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

class Schema800 implements SchemaInterface
{
    /**
     * Undocumented variable
     *
     * @var array
     */
    public $pattern = [
        [
            'paths' => [
                'resources/views/vendor/icore/web/profile/edit.blade.php',
                'resources/views/vendor/icore/auth/login.blade.php',
                'resources/views/vendor/icore/auth/register.blade.php',
                'resources/views/vendor/icore/admin/page/create.blade.php',
                'resources/views/vendor/icore/admin/page/edit_full.blade.php',
                'resources/views/vendor/icore/admin/post/create.blade.php',
                'resources/views/vendor/icore/admin/post/edit_full.blade.php',
                'resources/views/vendor/icore/web/components/newsletter.blade.php',
                'resources/views/vendor/icore/web/contact/show.blade.php'
            ],
            'actions' => [
                [
                    'type' => 'replace',
                    'search' => '/custom-checkbox/',
                    'to' => 'custom-switch'
                ]
            ]
        ]
    ];
}
