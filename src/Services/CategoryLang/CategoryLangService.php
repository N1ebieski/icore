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

namespace N1ebieski\ICore\Services\CategoryLang;

use Throwable;
use Illuminate\Config\Repository as Config;
use Illuminate\Database\DatabaseManager as DB;
use N1ebieski\ICore\Models\CategoryLang\CategoryLang;

class CategoryLangService
{
    /**
     *
     * @param CategoryLang $categoryLang
     * @param Config $config
     * @param DB $db
     * @return void
     */
    public function __construct(
        protected CategoryLang $categoryLang,
        protected Config $config,
        protected DB $db
    ) {
        //
    }

    /**
     *
     * @param array $attributes
     * @return CategoryLang
     * @throws Throwable
     */
    public function createOrUpdate(array $attributes): CategoryLang
    {
        return $this->db->transaction(function () use ($attributes) {
            if ($this->categoryLang->exists) {
                return $this->update($attributes);
            }

            return $this->create($attributes);
        });
    }

    /**
     *
     * @param array $attributes
     * @return CategoryLang
     * @throws Throwable
     */
    public function create(array $attributes): CategoryLang
    {
        return $this->db->transaction(function () use ($attributes) {
            $this->categoryLang->fill($attributes);

            $this->categoryLang->category()->associate($attributes['category']);

            $this->categoryLang->save();

            return $this->categoryLang;
        });
    }

    /**
     *
     * @param array $attributes
     * @return CategoryLang
     * @throws Throwable
     */
    public function update(array $attributes): CategoryLang
    {
        return $this->db->transaction(function () use ($attributes) {
            $this->categoryLang->fill($attributes);

            $this->categoryLang->save();

            return $this->categoryLang;
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
            return $this->categoryLang->delete();
        });
    }
}
