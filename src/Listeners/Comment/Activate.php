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

namespace N1ebieski\ICore\Listeners\Comment;

use N1ebieski\ICore\Models\User;
use Illuminate\Contracts\Auth\Guard as Auth;
use N1ebieski\ICore\ValueObjects\Comment\Status;
use N1ebieski\ICore\Events\Interfaces\Comment\CommentEventInterface;

class Activate
{
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
     * Handle the event.
     *
     * @param  CommentEventInterface  $event
     * @return void
     */
    public function handle($event)
    {
        /** @var User */
        $user = $this->auth->user();

        if ($user->can('web.comments.create')) {
            $event->comment->update(['status' => Status::ACTIVE]);
        }
    }
}
