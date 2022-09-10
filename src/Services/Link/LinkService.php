<?php

/**
 * NOTICE OF LICENSE
 *
 * This source file is licenced under the Software License Agreement
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://intelekt.net.pl/pages/regulamin
 *
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * @author    Mariusz Wysokiński <kontakt@intelekt.net.pl>
 * @copyright Since 2019 INTELEKT - Usługi Komputerowe Mariusz Wysokiński
 * @license   https://intelekt.net.pl/pages/regulamin
 */

namespace N1ebieski\ICore\Services\Link;

use Throwable;
use N1ebieski\ICore\Models\Link;
use N1ebieski\ICore\Utils\File\File;
use Illuminate\Database\DatabaseManager as DB;

class LinkService
{
    /**
     * Undocumented function
     *
     * @param Link $link
     * @param File $file
     * @param DB $db
     */
    public function __construct(
        protected Link $link,
        protected File $file,
        protected DB $db
    ) {
        //
    }

    /**
     *
     * @param array $attributes
     * @return Link
     * @throws Throwable
     */
    public function create(array $attributes): Link
    {
        return $this->db->transaction(function () use ($attributes) {
            $this->link->fill($attributes);

            if (isset($attributes['img'])) {
                $this->link->img_url = $this->file->makeFromFile($attributes['img'])
                    ->upload($this->link->path) ?: null;
            }

            $this->link->save();

            if (array_key_exists('categories', $attributes)) {
                $this->link->categories()->attach($attributes['categories'] ?? []);
            }

            return $this->link;
        });
    }

    /**
     *
     * @param array $attributes
     * @return Link
     * @throws Throwable
     */
    public function update(array $attributes): Link
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
                $this->link->img_url = $this->file->makeFromFile($attributes['img'])
                    ->upload($this->link->path) ?: null;
            }

            $this->link->save();

            if (array_key_exists('categories', $attributes)) {
                $this->link->categories()->sync($attributes['categories'] ?? []);
            }

            return $this->link;
        });
    }

    /**
     *
     * @param int $position
     * @return bool
     * @throws Throwable
     */
    public function updatePosition(int $position): bool
    {
        return $this->db->transaction(function () use ($position) {
            return $this->link->update(['position' => $position]);
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
