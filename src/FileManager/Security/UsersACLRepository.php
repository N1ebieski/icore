<?php

namespace N1ebieski\ICore\FileManager\Security;

use Illuminate\Contracts\Auth\Guard as Auth;
use Alexusmai\LaravelFileManager\Services\ACLService\ACLRepository;

class UsersACLRepository implements ACLRepository
{
    /**
     * @var int
     */
    private const ACCESS_DENIED = 0;

    /**
     * @var int
     */
    private const ACCESS_READ = 1;

    /**
     * @var int
     */
    private const ACCESS_READ_WRITE = 2;

    /**
     * Undocumented variable
     *
     * @var Auth
     */
    private $auth;

    /**
     * Undocumented function
     *
     * @param Auth $auth
     */
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
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
