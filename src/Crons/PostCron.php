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

namespace N1ebieski\ICore\Crons;

use N1ebieski\ICore\Models\Post;

class PostCron
{
    /**
     * [__construct description]
     * @param Post $post [description]
     */
    public function __construct(protected Post $post)
    {
        //
    }

    /**
     * [__invoke description]
     */
    public function __invoke(): void
    {
        $this->publicateScheduled();
    }

    /**
     * Activates all scheduled posts with a date earlier than now()
     *
     * @return int [description]
     */
    private function publicateScheduled(): int
    {
        return $this->post->makeService()->activateScheduled();
    }
}
