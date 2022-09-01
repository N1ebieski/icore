<?php

namespace N1ebieski\ICore\Filters\Traits;

trait HasSearch
{
    /**
     *
     * @param string|null $value
     * @return void
     */
    public function filterSearch(string $value = null): void
    {
        $this->parameters['search'] = $value;
    }
}
