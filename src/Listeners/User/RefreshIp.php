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

namespace N1ebieski\ICore\Listeners\User;

use Illuminate\Http\Request;
use N1ebieski\ICore\Models\User;
use Illuminate\Contracts\Auth\Guard as Auth;

class RefreshIp
{
    /**
     * Undocumented function
     *
     * @param Auth $auth
     */
    public function __construct(protected Auth $auth, protected Request $request)
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event): void
    {
        /** @var User */
        $user = $this->auth->user();

        $user->update([
            'ip' => $this->request->ip()
        ]);
    }
}
