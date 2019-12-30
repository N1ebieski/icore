<?php

namespace N1ebieski\ICore\Filters\Traits;

/**
 * [trait description]
 */
trait HasPaginate
{
    /**
     * [filterPaginate description]
     * @param int|null $value [description]
     */
     public function filterPaginate(int $value = null) : void
     {
         $this->parameters['paginate'] = $value;
     }
}
