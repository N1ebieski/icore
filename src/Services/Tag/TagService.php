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

namespace N1ebieski\ICore\Services\Tag;

use Throwable;
use N1ebieski\ICore\Models\Tag\Tag;
use Illuminate\Database\DatabaseManager as DB;

class TagService
{
    /**
     * Undocumented function
     *
     * @param Tag $tag
     * @param DB $db
     */
    public function __construct(
        protected Tag $tag,
        protected DB $db
    ) {
        //
    }

    /**
     *
     * @param array $attributes
     * @return Tag
     * @throws Throwable
     */
    public function create(array $attributes): Tag
    {
        return $this->db->transaction(function () use ($attributes) {
            return $this->tag->create($attributes);
        });
    }

    /**
     *
     * @param array $attributes
     * @return Tag
     * @throws Throwable
     */
    public function update(array $attributes): Tag
    {
        return $this->db->transaction(function () use ($attributes) {
            $this->tag->update($attributes);

            return $this->tag;
        });
    }

    /**
     *
     * @return null|bool
     * @throws Throwable
     */
    public function delete(): ?bool
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
