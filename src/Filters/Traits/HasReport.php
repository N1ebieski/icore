<?php

namespace N1ebieski\ICore\Filters\Traits;

trait HasReport
{
    /**
     *
     * @param int|null $value
     * @return void
     */
    public function filterReport(int $value = null): void
    {
        $this->parameters['report'] = $value;
    }
}
