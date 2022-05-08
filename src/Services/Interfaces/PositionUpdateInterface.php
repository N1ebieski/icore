<?php

namespace N1ebieski\ICore\Services\Interfaces;

interface PositionUpdateInterface
{
    /**
     * [updatePosition description]
     * @param  array $attributes [description]
     * @return bool              [description]
     */
    public function updatePosition(array $attributes): bool;
}
