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
use Illuminate\Http\Response as HttpResponse;
use N1ebieski\ICore\Models\Category\Post\Category;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CategoryTest extends TestCase
{
    use DatabaseTransactions;

    public function testNoexistCategoryShow(): void
    {
        $response = $this->get(route('web.category.post.show', ['dajskruiufi']));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testCategoryShow(): void
    {
        /** @var Category */
        $category = Category::makeFactory()->active()->create();

        $response = $this->get(route('web.category.post.show', [$category->slug]));

        $response->assertViewIs('icore::web.category.post.show');
    }

    public function testCategoryShowPaginate(): void
    {
        /** @var Category */
        $category = Category::makeFactory()->active()->create();

        /** @var array<Post> */
        $posts = Post::makeFactory()->count(50)->active()->withUser()->hasAttached($category)
            ->sequence(function (Sequence $sequence) {
                return [
                    'published_at' => Carbon::now()->addMinutes($sequence->index)
                ];
            })
            ->create();

        $response = $this->get(route('web.category.post.show', [$category->slug, 'page' => 2]));

        $response->assertSee('class="pagination"', false)
            ->assertSeeInOrder([$category->name, $posts[10]->title], false);
    }
}
