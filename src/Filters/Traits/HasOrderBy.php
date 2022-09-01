<?php

namespace N1ebieski\ICore\Filters\Traits;

trait HasOrderBy
{
    /**
     * 
     * @param string|null $value 
     * @return void 
     */
    public function filterOrderBy(string $value = null): void
    {
        $this->parameters['orderby'] = $value;
    }
}
