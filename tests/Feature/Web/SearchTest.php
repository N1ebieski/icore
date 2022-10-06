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

namespace N1ebieski\ICore\Tests\Feature\Web;

use Tests\TestCase;
use N1ebieski\ICore\Models\Post;

class SearchTest extends TestCase
{
    public function testSearchValidationFail(): void
    {
        $response = $this->get(route('web.search.index'), [
            'search' => 'fd'
        ]);

        $response->assertSessionHasErrors(['search', 'source']);
    }

    public function testSearch(): void
    {
        /** @var Post */
        $post = Post::makeFactory()->active()->publish()->withUser()->create([
            'title' => 'Post należący do testów w phpunit'
        ]);

        $response = $this->followingRedirects()->get(route('web.search.index', [
            'search' => 'należący do',
            'source' => 'post'
        ]));

        $response->assertViewIs('icore::web.post.search');
        $response->assertSeeInOrder([$post->title, $post->content_html], false);

        // Wyszukiwanie odbywa się przez AGAINST MATCH po indeksie.
        // Nie mogę użyć DatabaseTransactions.
        $post->delete();
    }
}
