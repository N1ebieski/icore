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

namespace N1ebieski\ICore\Loads\Web\Rating\Comment;

use Illuminate\Http\Request;
use N1ebieski\ICore\Models\User;
use N1ebieski\ICore\Models\Comment\Comment;
use N1ebieski\ICore\Models\Rating\Comment\Rating;

class RateLoad
{
    /**
     *
     * @param Request $request
     * @param Rating $rating
     * @return void
     */
    public function __construct(Request $request, protected Rating $rating)
    {
        /** @var Comment */
        $comment = $request->route('comment');

        /** @var User */
        $user = $request->user();

        /** @var Rating|null */
        $commentRating = $comment->makeRepo()->firstRatingByUser($user);

        $this->rating = $commentRating ?? $rating;
    }

    /**
     * Get the value of rating
     *
     * @return  Rating
     */
    public function getRating(): Rating
    {
        return $this->rating;
    }
}
