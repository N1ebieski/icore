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

namespace N1ebieski\ICore\Filters\Traits;

use N1ebieski\ICore\Models\Category\Category;

trait HasCategory
{
    /**
     *
     * @param Category $category
     * @return self
     */
    public function setCategory(Category $category): self
    {
        $this->parameters['category'] = $category;

        return $this;
    }

    /**
     *
     * @param int|null $id
     * @return void
     */
    public function filterCategory(int $id = null): void
    {
        $this->parameters['category'] = null;

        if ($id === 0) {
            $this->parameters['category'] = 0;
        }

        if ($id !== null) {
            if ($category = $this->findCategory($id)) {
                $this->setCategory($category);
            }
        }
    }

    /**
     * [findCategory description]
     * @param  int   $id [description]
     * @return Category|null     [description]
     */
    public function findCategory(int $id): ?Category
    {
        return Category::withAncestorsExceptSelf()
            ->where('id', $id)
            ->first();
    }
}
