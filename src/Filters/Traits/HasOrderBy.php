<?php

namespace N1ebieski\ICore\Filters\Traits;

/**
 * [trait description]
 */
trait HasOrderBy
{
    /**
     * [filterOrderBy description]
     * @param string|null $value [description]
     */
    public function filterOrderBy(string $value = null) : void
    {
        $this->parameters['orderby'] = $value;
    }
}
