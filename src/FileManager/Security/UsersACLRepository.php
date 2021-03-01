<?php

namespace N1ebieski\ICore\FileManager\Security;

use Illuminate\Contracts\Auth\Guard as Auth;
use Alexusmai\LaravelFileManager\Services\ACLService\ACLRepository;

class UsersACLRepository implements ACLRepository
{
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
    public function getRules() : array
    {
        return [
            ['disk' => 'public', 'path' => '.gitignore', 'access' => 0],
            ['disk' => 'public', 'path' => 'vendor', 'access' => $this->hasPermission() !== 0 ? 1 : 0],
            ['disk' => 'public', 'path' => 'vendor/*', 'access' => $this->hasPermission() !== 0 ? 1 : 0],
            ['disk' => 'public', 'path' => '/', 'access' => $this->hasPermission()],
            ['disk' => 'public', 'path' => '*', 'access' => $this->hasPermission()],

            ['disk' => 'views', 'path' => '/', 'access' => $this->auth->user()->hasRole('super-admin') ? 2 : 0],
            ['disk' => 'views', 'path' => '*', 'access' => $this->auth->user()->hasRole('super-admin') ? 2 : 0],

            ['disk' => 'lang', 'path' => '/', 'access' => $this->auth->user()->hasRole('super-admin') ? 2 : 0],
            ['disk' => 'lang', 'path' => '*', 'access' => $this->auth->user()->hasRole('super-admin') ? 2 : 0],

            ['disk' => 'css', 'path' => '/', 'access' => $this->auth->user()->hasRole('super-admin') ? 2 : 0],
            ['disk' => 'css', 'path' => '*', 'access' => $this->auth->user()->hasRole('super-admin') ? 2 : 0],

            ['disk' => 'js', 'path' => '/', 'access' => $this->auth->user()->hasRole('super-admin') ? 2 : 0],
            ['disk' => 'js', 'path' => '*', 'access' => $this->auth->user()->hasRole('super-admin') ? 2 : 0],

            ['disk' => 'images', 'path' => '/', 'access' => $this->auth->user()->hasRole('super-admin') ? 2 : 0],
            ['disk' => 'images', 'path' => '*', 'access' => $this->auth->user()->hasRole('super-admin') ? 2 : 0],
            
            ['disk' => 'svg', 'path' => '/', 'access' => $this->auth->user()->hasRole('super-admin') ? 2 : 0],
            ['disk' => 'svg', 'path' => '*', 'access' => $this->auth->user()->hasRole('super-admin') ? 2 : 0]
        ];
    }

    /**
     * Undocumented function
     *
     * @return integer
     */
    protected function hasPermission() : int
    {
        if ($this->auth->user()->can('admin.filemanager.read')
        && $this->auth->user()->can('admin.filemanager.write')) {
            return 2;
        }

        if ($this->auth->user()->can('admin.filemanager.read')) {
            return 1;
        }

        return 0;
    }
}
