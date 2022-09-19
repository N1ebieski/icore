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

namespace N1ebieski\ICore\Attributes\Post;

use N1ebieski\ICore\Models\Post;
use Illuminate\Database\Eloquent\Casts\Attribute;

class FirstImage
{
    /**
     *
     * @param Post $post
     * @return void
     */
    public function __construct(protected Post $post)
    {
        //
    }

    /**
     *
     * @return Attribute
     */
    public function __invoke(): Attribute
    {
        return new Attribute(
            get: function (): ?string {
                preg_match('/<img.+src=(?:\'|")(.*?)(?:\'|")/', $this->post->content_html, $image);

                return $image[1] ?? null;
            }
        );
    }
}
