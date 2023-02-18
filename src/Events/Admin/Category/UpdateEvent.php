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

namespace N1ebieski\ICore\Events\Admin\Category;

use Illuminate\Queue\SerializesModels;
use N1ebieski\ICore\Models\Category\Category;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use N1ebieski\ICore\Events\Interfaces\AutoTranslateEventInterface;
use N1ebieski\ICore\Events\Interfaces\Category\CategoryEventInterface;

class UpdateEvent implements CategoryEventInterface, AutoTranslateEventInterface
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     *
     * @var Category
     */
    public Category $model;

    /**
     * Create a new event instance.
     *
     * @param Category         $category    [description]
     * @return void
     */
    public function __construct(public Category $category)
    {
        $this->model = $category;
    }
}
