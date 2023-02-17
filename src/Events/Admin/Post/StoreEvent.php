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

namespace N1ebieski\ICore\Events\Admin\Post;

use N1ebieski\ICore\Models\Post;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use N1ebieski\ICore\Events\Interfaces\Post\PostEventInterface;
use N1ebieski\ICore\Events\Interfaces\AutoTranslateEventInterface;

class StoreEvent implements PostEventInterface, AutoTranslateEventInterface
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     *
     * @var Post
     */
    public Post $model;

    /**
     * Create a new event instance.
     *
     * @param Post         $post    [description]
     * @return void
     */
    public function __construct(public Post $post)
    {
        $this->model = $post;
    }
}
