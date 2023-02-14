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

namespace N1ebieski\ICore\Crons\AutoTranslate\Builder;

use Closure;
use N1ebieski\ICore\Models\Post;
use Illuminate\Contracts\Container\Container as App;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\Container\BindingResolutionException;
use N1ebieski\ICore\Crons\AutoTranslate\Builder\Interfaces\BuilderInterface;

class PostBuilder implements BuilderInterface
{
    /**
     *
     * @param Post $post
     * @param Config $config
     * @param App $app
     * @return void
     */
    public function __construct(
        protected Post $post,
        protected Config $config,
        protected App $app
    ) {
        //
    }

    /**
     *
     * @param Closure $closure
     * @param string|null $timestamp
     * @return bool
     * @throws BindingResolutionException
     */
    public function chunkCollection(Closure $closure, string $timestamp = null): bool
    {
        return $this->post->makeRepo()->chunkAutoTransWithLangsByTranslatedAt($closure, $timestamp);
    }
}
