<?php

namespace N1ebieski\ICore\Filters\Traits;

trait HasExcept
{
    /**
     * [filterExcept description]
     * @param int|null $value [description]
     */
    public function filterExcept(array $value = null): void
    {
        $this->parameters['except'] = $value;
    }
}
