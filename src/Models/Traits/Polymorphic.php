<?php

namespace N1ebieski\ICore\Models\Traits;

use Illuminate\Database\Eloquent\Builder;

/**
 * [trait description]
 */
trait Polymorphic
{
    // Scopes

    /**
     * [scopePoliType description]
     * @param  Builder $query      [description]
     * @return Builder|null              [description]
     */
    public function scopePoliType(Builder $query) : ?Builder
    {
        return $query->when($this->model_type !== null, function($query) {
            $query->where('model_type', $this->model_type);
        });
    }

    /**
     * [scopePoli description]
     * @param  Builder $query [description]
     * @return Builder|null        [description]
     */
    public function scopePoli(Builder $query) : ?Builder
    {
        return $query->when($this->model_id !== null, function($query) {
            $query->poliType()->where('model_id', $this->model_id);
        });
    }

    // Accessors

    /**
     * [getPoliAttribute description]
     * @return string [description]
     */
    public function getPoliAttribute() : string
    {
        $type = substr($this->model_type, strrpos($this->model_type, "\\") + 1);

        return strtolower($type);
    }
}
