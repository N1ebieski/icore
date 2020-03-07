<?php

namespace N1ebieski\ICore\Filters\Traits;

/**
 * [trait description]
 */
trait HasParent
{
    /**
     * [filterParent description]
     * @param  int|null $id [description]
     */
    public function filterParent(int $id = null)
    {
        $this->parameters['parent'] = null;

        if ($id === 0) {
            return $this->parameters['parent'] = 0;
        }

        if ($id !== null) {
            if ($parent = $this->findParent($id)) {
                return $this->setParent($parent);
            }
        }
    }
}
