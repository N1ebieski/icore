<?php

namespace N1ebieski\ICore\Services;

use N1ebieski\ICore\Models\Link;
use N1ebieski\ICore\Utils\File\File;
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
     * @var File
     */
    protected $file;

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
     * @param File $file
     * @param DB $db
     */
    public function __construct(Link $link, File $file, DB $db)
    {
        $this->link = $link;

        $this->file = $file;

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
            $this->link->fill($attributes);

            if (isset($attributes['img'])) {
                $this->link->img_url = $this->file->makeFromFile($attributes['img'])->upload($this->link->path);
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
                    $this->file->delete($this->link->img_url);
                }

                $this->link->img_url = null;
            }

            if (isset($attributes['img'])) {
                $this->link->img_url = $this->file->makeFromFile($attributes['img'])->upload($this->link->path);
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
                $this->file->delete($this->link->img_url);
            }

            $this->link->categories()->detach();

            return $this->link->delete();
        });
    }
}
