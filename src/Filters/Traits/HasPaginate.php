<?php

namespace N1ebieski\ICore\Filters\Traits;

trait HasPaginate
{
    /**
     * 
     * @param int|null $value 
     * @return void 
     */
    public function filterPaginate(int $value = null): void
    {
        $this->parameters['paginate'] = $value;
    }
}
