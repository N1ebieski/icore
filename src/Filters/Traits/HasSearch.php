<?php

namespace N1ebieski\ICore\Filters\Traits;

trait HasSearch
{
    /**
     * [filterSearch description]
     * @param string|null $value [description]
     */
    public function filterSearch(string $value = null): void
    {
        $this->parameters['search'] = $value;
    }
}
