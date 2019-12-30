<?php

namespace N1ebieski\ICore\Filters\Traits;

/**
 * [trait description]
 */
trait HasStatus
{
    /**
     * [filterStatus description]
     * @param int|null $value [description]
     */
    public function filterStatus(int $value = null) : void
    {
        $this->parameters['status'] = $value;
    }
}
