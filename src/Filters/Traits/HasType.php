<?php

namespace N1ebieski\ICore\Filters\Traits;

trait HasType
{
    /**
     * [filterType description]
     * @param string|null $value [description]
     */
    public function filterType(string $value = null) : void
    {
        $this->parameters['type'] = $value;
    }
}
