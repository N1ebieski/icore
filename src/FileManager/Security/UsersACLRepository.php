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

namespace N1ebieski\ICore\FileManager\Security;

use Illuminate\Contracts\Auth\Guard as Auth;
use Alexusmai\LaravelFileManager\Services\ACLService\ACLRepository;

class UsersACLRepository implements ACLRepository
{
    /**
     * @var int
     */
    protected const ACCESS_DENIED = 0;

    /**
     * @var int
     */
    protected const ACCESS_READ = 1;

    /**
     * @var int
     */
    protected const ACCESS_READ_WRITE = 2;

    /**
     * Undocumented function
     *
     * @param Auth $auth
     */
    public function __construct(protected Auth $auth)
    {
        //
    }

    /**
     * Get user ID
     *
     * @return mixed
     */
    public function getUserID()
    {
        return $this->auth->id();
    }

    /**
     * Get ACL rules list for user
     *
     * @return array
     */
    public function getRules(): array
    {
        return [
            [
                'disk' => 'public',
                'path' => '.gitignore',
                'access' => self::ACCESS_DENIED
            ],
            [
                'disk' => 'public',
                'path' => 'vendor',
                'access' => $this->hasPermission() !== self::ACCESS_DENIED ?
                    self::ACCESS_READ
                    : self::ACCESS_DENIED
            ],
            [
                'disk' => 'public',
                'path' => 'vendor/*',
                'access' => $this->hasPermission() !== self::ACCESS_DENIED ?
                    self::ACCESS_READ
                    : self::ACCESS_DENIED
            ],
            [
                'disk' => 'public',
                'path' => '/',
                'access' => $this->hasPermission()
            ],
            [
                'disk' => 'public',
                'path' => '*',
                'access' => $this->hasPermission()
            ],
            [
                'disk' => 'views',
                'path' => '/',
                'access' => $this->hasPermission()
            ],
            [
                'disk' => 'views',
                'path' => '*',
                'access' => $this->hasPermission()
            ],
            [
                'disk' => 'lang',
                'path' => '/',
                'access' => $this->hasPermission()
            ],
            [
                'disk' => 'lang',
                'path' => '*',
                'access' => $this->hasPermission()
            ],
            [
                'disk' => 'css',
                'path' => '/',
                'access' => $this->hasPermission()
            ],
            [
                'disk' => 'css',
                'path' => '*',
                'access' => $this->hasPermission()
            ]
        ];
    }

    /**
     * Undocumented function
     *
     * @return integer
     */
    protected function hasPermission(): int
    {
        if (
            $this->auth->user()->can('admin.filemanager.read')
            && $this->auth->user()->can('admin.filemanager.write')
        ) {
            return self::ACCESS_READ_WRITE;
        }

        if ($this->auth->user()->can('admin.filemanager.read')) {
            return self::ACCESS_READ;
        }

        return self::ACCESS_DENIED;
    }
}
