<?php

namespace N1ebieski\ICore\Filters\Traits;

trait HasType
{
    /**
     *
     * @param string|null $value
     * @return void
     */
    public function filterType(string $value = null): void
    {
        $this->parameters['type'] = $value;
    }
}
