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
use Illuminate\Support\Carbon;
use N1ebieski\ICore\Models\Post;
use Carbon\Carbon as CarbonFormat;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ArchiveTest extends TestCase
{
    use DatabaseTransactions;

    public function testNoexistArchiveShow(): void
    {
        $response = $this->get(route('web.archive.post.show', ['month' => 99, 'year' => 99999]));

        $response->assertSessionHasErrors(['month', 'year']);
    }

    public function testArchiveShow(): void
    {
        $response = $this->get(route('web.archive.post.show', ['month' => 12, 'year' => 2018]));

        $response->assertViewIs('icore::web.archive.post.show');
    }

    public function testArchiveShowPaginate(): void
    {
        /** @var Collection<Post>|array<Post> */
        $posts = Post::makeFactory()->count(50)->active()->withUser()
            ->sequence(function (Sequence $sequence) {
                /** @var CarbonFormat */
                $dateTime = Carbon::createFromFormat('Y-m-d', '2018-12-23');

                return [
                    'published_at' => $dateTime->addMinutes($sequence->index)
                ];
            })
            ->create();

        $response = $this->get(route('web.archive.post.show', ['month' => 12, 'year' => 2018, 'page' => 2]));

        $response->assertViewIs('icore::web.archive.post.show')
            ->assertSee('class="pagination"', false)
            ->assertSeeText($posts[10]->title, false);
    }
}
