<?php

namespace N1ebieski\ICore\Services\Interfaces;

interface StatusUpdatable
{
    /**
     * [updateStatus description]
     * @param  array $attributes [description]
     * @return bool              [description]
     */
    public function updateStatus(array $attributes): bool;
}
