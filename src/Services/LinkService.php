<?php

namespace N1ebieski\ICore\Services;

use N1ebieski\ICore\Models\Link;
use Illuminate\Http\UploadedFile;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\DatabaseManager as DB;
use N1ebieski\ICore\Services\Interfaces\Creatable;
use N1ebieski\ICore\Services\Interfaces\Deletable;
use N1ebieski\ICore\Services\Interfaces\Updatable;
use Illuminate\Contracts\Filesystem\Factory as Storage;
use N1ebieski\ICore\Services\Interfaces\PositionUpdatable;

class LinkService implements Creatable, Updatable, PositionUpdatable, Deletable
{
    /**
     * Model
     * @var Link
     */
    protected $link;

    /**
     * [private description]
     * @var Storage
     */
    protected $storage;

    /**
     * Undocumented variable
     *
     * @var DB
     */
    protected $db;

    /**
     * [protected description]
     * @var string
     */
    protected $img_dir = 'vendor/icore/links';

    /**
     * Undocumented function
     *
     * @param Link $link
     * @param Storage $storage
     * @param DB $db
     */
    public function __construct(Link $link, Storage $storage, DB $db)
    {
        $this->link = $link;
        
        $this->storage = $storage;
        $this->db = $db;
    }

    /**
     * [create description]
     * @param  array $attributes [description]
     * @return Model             [description]
     */
    public function create(array $attributes) : Model
    {
        return $this->db->transaction(function () use ($attributes) {
            $this->link->fill($attributes);

            if (isset($attributes['img']) && $attributes['img'] instanceof UploadedFile) {
                $this->link->img_url = $this->uploadImage($attributes['img']);
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
    public function update(array $attributes) : bool
    {
        return $this->db->transaction(function () use ($attributes) {
            $this->link->fill($attributes);

            if (isset($attributes['delete_img'])) {
                $this->deleteImage();

                $this->link->img_url = null;
            }

            if (isset($attributes['img']) && $attributes['img'] instanceof UploadedFile) {
                $this->link->img_url = $this->uploadImage($attributes['img']);
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
    public function updatePosition(array $attributes) : bool
    {
        return $this->db->transaction(function () use ($attributes) {
            return $this->link->update(['position' => (int)$attributes['position']]);
        });
    }

    /**
     * [delete description]
     * @return bool [description]
     */
    public function delete() : bool
    {
        return $this->db->transaction(function () {
            $this->deleteImage();

            $this->link->categories()->detach();

            return $this->link->delete();
        });
    }

    /**
     * [uploadImage description]
     * @param  UploadedFile   $img [description]
     * @return string              [description]
     */
    protected function uploadImage(UploadedFile $img) : string
    {
        return $this->storage->disk('public')->putFile($this->link->path, $img);
    }

    /**
     * [deleteImage description]
     * @return bool [description]
     */
    protected function deleteImage() : bool
    {
        if ($this->link->img_url !== null) {
            if ($this->storage->disk('public')->exists($this->link->img_url)) {
                return $this->storage->disk('public')->delete($this->link->img_url);
            }
        }

        return false;
    }
}
