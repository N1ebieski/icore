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

namespace N1ebieski\ICore\Services\PostLang;

use Throwable;
use Illuminate\Config\Repository as Config;
use N1ebieski\ICore\Models\PostLang\PostLang;
use Illuminate\Database\DatabaseManager as DB;
use N1ebieski\ICore\Services\Interfaces\LangServiceInterface;

class PostLangService implements LangServiceInterface
{
    /**
     *
     * @param PostLang $postLang
     * @param Config $config
     * @param DB $db
     * @return void
     */
    public function __construct(
        protected PostLang $postLang,
        protected Config $config,
        protected DB $db
    ) {
        //
    }

    /**
     *
     * @param array $attributes
     * @return PostLang
     * @throws Throwable
     */
    public function createOrUpdate(array $attributes): PostLang
    {
        return $this->db->transaction(function () use ($attributes) {
            if ($this->postLang->exists) {
                return $this->update($attributes);
            }

            return $this->create($attributes);
        });
    }

    /**
     *
     * @param array $attributes
     * @return PostLang
     * @throws Throwable
     */
    public function create(array $attributes): PostLang
    {
        return $this->db->transaction(function () use ($attributes) {
            $this->postLang->fill($attributes);

            $this->postLang->content = $attributes['content_html'];

            $this->postLang->post()->associate($attributes['post']);

            $this->postLang->save();

            return $this->postLang;
        });
    }

    /**
     *
     * @param array $attributes
     * @return PostLang
     * @throws Throwable
     */
    public function update(array $attributes): PostLang
    {
        return $this->db->transaction(function () use ($attributes) {
            $this->postLang->fill($attributes);

            if (array_key_exists('content_html', $attributes)) {
                $this->postLang->content = $attributes['content_html'];
            }

            $this->postLang->save();

            return $this->postLang;
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
            $this->postLang->post->comments()->lang()->delete();

            // @phpstan-ignore-next-line
            $this->postLang->post->tags()->lang()->detach();

            return $this->postLang->delete();
        });
    }
}
