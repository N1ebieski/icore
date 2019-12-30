<?php

namespace N1ebieski\ICore\Services\Interfaces;

/**
 * [interface description]
 */
interface Updatable
{
    /**
     * [update description]
     * @param  array $attributes [description]
     * @return bool              [description]
     */
    public function update(array $attributes) : bool;
}
