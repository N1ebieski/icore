<?php

namespace N1ebieski\ICore\Services;

use N1ebieski\ICore\Models\Tag\Tag;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\DatabaseManager as DB;
use N1ebieski\ICore\Services\Interfaces\CreateInterface;
use N1ebieski\ICore\Services\Interfaces\DeleteInterface;
use N1ebieski\ICore\Services\Interfaces\UpdateInterface;
use N1ebieski\ICore\Services\Interfaces\GlobalDeleteInterface;

class TagService implements CreateInterface, UpdateInterface, DeleteInterface, GlobalDeleteInterface
{
    /**
     * [private description]
     * @var Tag
     */
    protected $tag;

    /**
     * Undocumented variable
     *
     * @var DB
     */
    protected $db;

    /**
     * Undocumented function
     *
     * @param Tag $tag
     * @param DB $db
     */
    public function __construct(Tag $tag, DB $db)
    {
        $this->tag = $tag;

        $this->db = $db;
    }

    /**
     * [create description]
     * @param  array $attributes [description]
     * @return Model             [description]
     */
    public function create(array $attributes): Model
    {
        return $this->db->transaction(function () use ($attributes) {
            return $this->tag->create($attributes);
        });
    }

    /**
     * Undocumented function
     *
     * @param array $attributes
     * @return boolean
     */
    public function update(array $attributes): bool
    {
        return $this->db->transaction(function () use ($attributes) {
            return $this->tag->update($attributes);
        });
    }

    /**
     * [delete description]
     * @return bool [description]
     */
    public function delete(): bool
    {
        return $this->db->transaction(function () {
            return $this->tag->delete();
        });
    }

    /**
     * [deleteGlobal description]
     * @param  array $ids [description]
     * @return int        [description]
     */
    public function deleteGlobal(array $ids): int
    {
        return $this->db->transaction(function () use ($ids) {
            return $this->tag->whereIn($this->tag->GetKeyName(), $ids)->delete();
        });
    }
}
