<?php

namespace N1ebieski\ICore\Filters\Traits;

/**
 * [trait description]
 */
trait HasCensored
{
    /**
     * [filterCensored description]
     * @param int|null $value [description]
     */
    public function filterCensored(int $value = null) : void
    {
        $this->parameters['censored'] = $value;
    }
}
