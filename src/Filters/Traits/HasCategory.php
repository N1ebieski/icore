<?php

namespace N1ebieski\ICore\Filters\Traits;

use N1ebieski\ICore\Models\Category\Category;

/**
 * [trait description]
 */
trait HasCategory
{
    /**
     * [setCategory description]
     * @param Category $category [description]
     */
    public function setCategory(Category $category)
    {
        $this->parameters['category'] = $category;

        return $this;
    }

    /**
     * [filterCategory description]
     * @param  int|null $id [description]
     * @return Category|0     [description]
     */
    public function filterCategory(int $id = null)
    {
        $this->parameters['category'] = null;

        if ($id === 0) {
            return $this->parameters['category'] = 0;
        }

        if ($id !== null) {
            if ($category = $this->findCategory($id)) {
                return $this->setCategory($category);
            }
        }
    }

    /**
     * [findCategory description]
     * @param  int|null   $id [description]
     * @return Category     [description]
     */
    public function findCategory(int $id = null): Category
    {
        return Category::withAncestorsExceptSelf()
            ->where('id', $id)
            ->first(['id', 'name']);
    }
}
