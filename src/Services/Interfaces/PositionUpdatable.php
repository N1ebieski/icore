<?php

namespace N1ebieski\ICore\Services\Interfaces;

/**
 * [interface description]
 */
interface PositionUpdatable
{
    /**
     * [updatePosition description]
     * @param  array $attributes [description]
     * @return bool              [description]
     */
    public function updatePosition(array $attributes) : bool;
}
