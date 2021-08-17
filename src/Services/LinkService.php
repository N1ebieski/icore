<?php

namespace N1ebieski\ICore\Services;

use N1ebieski\ICore\Models\Link;
use N1ebieski\ICore\Utils\FileUtil;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\DatabaseManager as DB;
use N1ebieski\ICore\Services\Interfaces\Creatable;
use N1ebieski\ICore\Services\Interfaces\Deletable;
use N1ebieski\ICore\Services\Interfaces\Updatable;
use N1ebieski\ICore\Services\Interfaces\PositionUpdatable;

class LinkService implements Creatable, Updatable, PositionUpdatable, Deletable
{
    /**
     * Model
     * @var Link
     */
    protected $link;

    /**
     * Undocumented variable
     *
     * @var FileUtil
     */
    protected $fileUtil;

    /**
     * Undocumented variable
     *
     * @var DB
     */
    protected $db;

    /**
     * Undocumented function
     *
     * @param Link $link
     * @param DB $db
     */
    public function __construct(Link $link, FileUtil $fileUtil, DB $db)
    {
        $this->setLink($link);

        $this->fileUtil = $fileUtil;

        $this->db = $db;
    }

    /**
     * Undocumented function
     *
     * @param Link $link
     * @return static
     */
    public function setLink(Link $link)
    {
        $this->link = $link;

        return $this;
    }

    /**
     * [create description]
     * @param  array $attributes [description]
     * @return Model             [description]
     */
    public function create(array $attributes): Model
    {
        return $this->db->transaction(function () use ($attributes) {
            $this->link->fill($attributes);

            if (isset($attributes['img'])) {
                $this->link->img_url = $this->fileUtil->make($attributes['img'], $this->link->path)->upload();
            }

            $this->link->save();

            if (array_key_exists('categories', $attributes)) {
                $this->link->categories()->attach($attributes['categories'] ?? []);
            }

            return $this->link;
        });
    }

    /**
     * [update description]
     * @param  array $attributes [description]
     * @return bool              [description]
     */
    public function update(array $attributes): bool
    {
        return $this->db->transaction(function () use ($attributes) {
            $this->link->fill($attributes);

            if (isset($attributes['delete_img'])) {
                if ($this->link->img_url !== null) {
                    $this->fileUtil->make($this->link->img_url)->delete();
                }

                $this->link->img_url = null;
            }

            if (isset($attributes['img'])) {
                $this->link->img_url = $this->fileUtil->make($attributes['img'], $this->link->path)->upload();
            }

            $link = $this->link->save();

            if (array_key_exists('categories', $attributes)) {
                $this->link->categories()->sync($attributes['categories'] ?? []);
            }

            return $link;
        });
    }

    /**
     * [updatePosition description]
     * @param  array $attributes [description]
     * @return bool              [description]
     */
    public function updatePosition(array $attributes): bool
    {
        return $this->db->transaction(function () use ($attributes) {
            return $this->link->update(['position' => (int)$attributes['position']]);
        });
    }

    /**
     * [delete description]
     * @return bool [description]
     */
    public function delete(): bool
    {
        return $this->db->transaction(function () {
            if ($this->link->img_url !== null) {
                $this->fileUtil->make(null, $this->link->img_url)->delete();
            }

            $this->link->categories()->detach();

            return $this->link->delete();
        });
    }
}
