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

namespace N1ebieski\ICore\Observers;

use N1ebieski\ICore\Models\User;
use Illuminate\Support\Facades\Cache;

class UserObserver
{
    /**
     * [private description]
     * @var bool
     */
    private static $pivotEvent = false;

    /**
     * Handle the user "created" event.
     *
     * @param  User  $user
     * @return void
     */
    public function created(User $user)
    {
        Cache::tags(['users'])->flush();
    }

    /**
     * Handle the user "updated" event.
     *
     * @param  User  $user
     * @return void
     */
    public function updated(User $user)
    {
        Cache::tags(['user.' . $user->id, 'users'])->flush();
    }

    /**
     *
     * @param User $user
     * @param mixed $relationName
     * @param mixed $pivotIds
     * @param mixed $pivotIdsAttributes
     * @return void
     */
    public function pivotAttached(User $user, $relationName, $pivotIds, $pivotIdsAttributes)
    {
        if (self::$pivotEvent === false && in_array($relationName, ['roles', 'socialites'])) {
            $this->updated($user);

            self::$pivotEvent = true;
        }
    }

    /**
     *
     * @param User $user
     * @param mixed $relationName
     * @param mixed $pivotIds
     * @return void
     */
    public function pivotDetached(User $user, $relationName, $pivotIds)
    {
        if (self::$pivotEvent === false && in_array($relationName, ['roles', 'socialites'])) {
            $this->updated($user);

            self::$pivotEvent = true;
        }
    }

    /**
     * Handle the user "deleted" event.
     *
     * @param  User  $user
     * @return void
     */
    public function deleted(User $user)
    {
        Cache::tags(['user.' . $user->id, 'users'])->flush();
    }

    /**
     * Handle the user "restored" event.
     *
     * @param  User  $user
     * @return void
     */
    public function restored(User $user)
    {
        //
    }

    /**
     * Handle the user "force deleted" event.
     *
     * @param  User  $user
     * @return void
     */
    public function forceDeleted(User $user)
    {
        //
    }
}
