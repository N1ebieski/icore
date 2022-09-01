<?php

namespace N1ebieski\ICore\Filters\Traits;

trait HasParent
{
    /**
     *
     * @param int|null $id
     * @return void
     */
    public function filterParent(int $id = null): void
    {
        $this->parameters['parent'] = null;

        if ($id === 0) {
            $this->parameters['parent'] = 0;
        }

        if ($id !== null) {
            if ($parent = $this->findParent($id)) {
                $this->setParent($parent);
            }
        }
    }
}
