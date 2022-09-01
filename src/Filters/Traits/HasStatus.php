<?php

namespace N1ebieski\ICore\Filters\Traits;

trait HasStatus
{
    /**
     *
     * @param int|null $value
     * @return void
     */
    public function filterStatus(int $value = null): void
    {
        $this->parameters['status'] = $value;
    }
}
