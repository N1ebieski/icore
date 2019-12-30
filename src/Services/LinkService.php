<?php

namespace N1ebieski\ICore\Services;

use Illuminate\Http\UploadedFile;
use N1ebieski\ICore\Models\Link;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Filesystem\Factory as Storage;
use N1ebieski\ICore\Services\Interfaces\Creatable;
use N1ebieski\ICore\Services\Interfaces\Updatable;
use N1ebieski\ICore\Services\Interfaces\PositionUpdatable;
use N1ebieski\ICore\Services\Interfaces\Deletable;

/**
 * [LinkService description]
 */
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
     * [protected description]
     * @var string
     */
    protected $img_dir = 'vendor/icore/links';

    /**
     * [__construct description]
     * @param Link $link [description]
     * @param Storage $storage [description]
     */
    public function __construct(Link $link, Storage $storage)
    {
        $this->link = $link;
        $this->storage = $storage;
    }

    /**
     * [create description]
     * @param  array $attributes [description]
     * @return Model             [description]
     */
    public function create(array $attributes) : Model
    {
        $this->link->fill($attributes);

        if (isset($attributes['img']) && $attributes['img'] instanceof UploadedFile) {
            $this->link->img_url = $this->uploadImage($attributes['img']);
        }

        $this->link->save();

        $this->link->categories()->attach($attributes['categories'] ?? []);

        return $this->link;
    }

    /**
     * [uploadImage description]
     * @param  UploadedFile   $img [description]
     * @return string              [description]
     */
    protected function uploadImage(UploadedFile $img) : string
    {
        return $this->storage->disk('public')->putFile($this->img_dir, $img);
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

    /**
     * [update description]
     * @param  array $attributes [description]
     * @return bool              [description]
     */
    public function update(array $attributes) : bool
    {
        $this->link->fill($attributes);

        if (isset($attributes['delete_img'])) {
            $this->deleteImage();
            $this->link->img_url = null;
        }

        if (isset($attributes['img']) && $attributes['img'] instanceof UploadedFile) {
            $this->link->img_url = $this->uploadImage($attributes['img']);
        }

        $link = $this->link->save();

        $this->link->categories()->sync($attributes['categories'] ?? []);

        return $link;
    }

    /**
     * [updatePosition description]
     * @param  array $attributes [description]
     * @return bool              [description]
     */
    public function updatePosition(array $attributes) : bool
    {
        return $this->link->update(['position' => (int)$attributes['position']]);
    }

    /**
     * [delete description]
     * @return bool [description]
     */
    public function delete() : bool
    {
        $this->deleteImage();

        $this->link->categories()->detach();

        return $this->link->delete();
    }
}
