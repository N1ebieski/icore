<?php

namespace N1ebieski\ICore\Services;

use Illuminate\Database\Eloquent\Model;

/**
 * [abstract description]
 */
interface Serviceable
{
    /**
     * [create description]
     * @param  array $attributes [description]
     * @return Model             [description]
     */
    public function create(array $attributes) : Model;

    /**
     * [update description]
     * @param  array $attributes [description]
     * @return bool              [description]
     */
    public function update(array $attributes) : bool;

    /**
     * [updateStatus description]
     * @param  array $attributes [description]
     * @return bool              [description]
     */
    public function updateStatus(array $attributes) : bool;

    /**
     * [delete description]
     * @return bool [description]
     */
    public function delete() : bool;

    /**
     * [deleteGlobal description]
     * @param  array $ids [description]
     * @return int        [description]
     */
    public function deleteGlobal(array $ids) : int;
}
