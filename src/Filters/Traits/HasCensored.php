<?php

namespace N1ebieski\ICore\Filters\Traits;

trait HasCensored
{
    /**
     *
     * @param int|null $value
     * @return void
     */
    public function filterCensored(int $value = null): void
    {
        $this->parameters['censored'] = $value;
    }
}
