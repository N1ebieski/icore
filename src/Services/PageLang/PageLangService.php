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

namespace N1ebieski\ICore\Services\PageLang;

use Throwable;
use Illuminate\Config\Repository as Config;
use N1ebieski\ICore\Models\PageLang\PageLang;
use Illuminate\Database\DatabaseManager as DB;

class PageLangService
{
    /**
     *
     * @param PageLang $pageLang
     * @param Config $config
     * @param DB $db
     * @return void
     */
    public function __construct(
        protected PageLang $pageLang,
        protected Config $config,
        protected DB $db
    ) {
        //
    }

    /**
     *
     * @param array $attributes
     * @return PageLang
     * @throws Throwable
     */
    public function createOrUpdate(array $attributes): PageLang
    {
        return $this->db->transaction(function () use ($attributes) {
            if ($this->pageLang->exists) {
                return $this->update($attributes);
            }

            return $this->create($attributes);
        });
    }

    /**
     *
     * @param array $attributes
     * @return PageLang
     * @throws Throwable
     */
    public function create(array $attributes): PageLang
    {
        return $this->db->transaction(function () use ($attributes) {
            $this->pageLang->fill($attributes);
            $this->pageLang->content = $this->pageLang->content_html;

            $this->pageLang->page()->associate($attributes['page']);

            $this->pageLang->save();

            return $this->pageLang;
        });
    }

    /**
     *
     * @param array $attributes
     * @return PageLang
     * @throws Throwable
     */
    public function update(array $attributes): PageLang
    {
        return $this->db->transaction(function () use ($attributes) {
            $this->pageLang->fill($attributes);
            $this->pageLang->content = $this->pageLang->content_html;

            $this->pageLang->save();

            return $this->pageLang;
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
            $this->pageLang->page->comments()->lang()->delete();

            $this->pageLang->page->tags()->lang()->detach();

            return $this->pageLang->delete();
        });
    }
}
