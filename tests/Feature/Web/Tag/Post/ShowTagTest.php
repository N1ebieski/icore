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

namespace N1ebieski\ICore\Tests\Feature\Web\Tag\Post;

use Carbon\Carbon;
use Tests\TestCase;
use N1ebieski\ICore\Models\Post;
use N1ebieski\ICore\Models\Tag\Post\Tag;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ShowTagTest extends TestCase
{
    use DatabaseTransactions;

    public function testShowNoExist(): void
    {
        $response = $this->get(route('web.tag.post.show', ['dajskruiufi-2']));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testShowPaginate(): void
    {
        /** @var Collection<Post>|array<Post> $posts */
        $posts = Post::makeFactory()->count(50)->active()->withUser()
            ->sequence(function (Sequence $sequence) {
                return [
                    'published_at' => Carbon::now()->addMinutes($sequence->index)
                ];
            })
            ->create();

        /** @var Tag $tag */
        $tag = Tag::makeFactory()->hasAttached($posts, [], 'morphs')->create();

        $response = $this->get(route('web.tag.post.show', [$tag->normalized, 'page' => 2]));

        $response->assertViewIs('icore::web.tag.post.show');
        $response->assertSee('class="pagination"', false);

        $response->assertSeeInOrder([$tag->name, $posts[10]->title], false);
    }
}
