<?php

namespace N1ebieski\ICore\Services\Interfaces;

/**
 * [interface description]
 */
interface FullUpdatable
{
    /**
     * [updateFull description]
     * @param  array $attributes [description]
     * @return bool              [description]
     */
    public function updateFull(array $attributes) : bool;
}
