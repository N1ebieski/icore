<?php

namespace N1ebieski\ICore\Filters\Traits;

/**
 * [trait description]
 */
trait HasReport
{
    /**
     * [filterReport description]
     * @param int|null $value [description]
     */
    public function filterReport(int $value = null) : void
    {
        $this->parameters['report'] = $value;
    }
}
