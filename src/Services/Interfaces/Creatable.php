<?php

namespace N1ebieski\ICore\Services\Interfaces;

use Illuminate\Database\Eloquent\Model;

/**
 * [interface description]
 */
interface Creatable
{
    /**
     * [create description]
     * @param  array $attributes [description]
     * @return Model             [description]
     */
    public function create(array $attributes) : Model;
}
