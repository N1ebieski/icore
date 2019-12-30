<?php

namespace N1ebieski\ICore\Services\Interfaces;

/**
 * [interface description]
 */
interface StatusUpdatable
{
    /**
     * [updateStatus description]
     * @param  array $attributes [description]
     * @return bool              [description]
     */
    public function updateStatus(array $attributes) : bool;
}
