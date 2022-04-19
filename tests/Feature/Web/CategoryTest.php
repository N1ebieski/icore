<?php

namespace N1ebieski\ICore\Tests\Feature\Web;

use Carbon\Carbon;
use Tests\TestCase;
use N1ebieski\ICore\Models\Post;
use Illuminate\Http\Response as HttpResponse;
use N1ebieski\ICore\Models\Category\Post\Category;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CategoryTest extends TestCase
{
    use DatabaseTransactions;

    public function testNoexistCategoryShow()
    {
        $response = $this->get(route('web.category.post.show', ['dajskruiufi']));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testCategoryShow()
    {
        $category = Category::makeFactory()->active()->create();

        $response = $this->get(route('web.category.post.show', [$category->slug]));

        $response->assertViewIs('icore::web.category.post.show');
    }

    public function testCategoryShowPaginate()
    {
        $category = Category::makeFactory()->active()->create();

        $post = Post::makeFactory()->count(50)->active()->withUser()->hasAttached($category)
            ->sequence(function ($sequence) {
                return [
                    'published_at' => Carbon::now()->addMinutes($sequence->index)
                ];
            })
            ->create();

        $response = $this->get(route('web.category.post.show', [$category->slug, 'page' => 2]));

        $response->assertSee('class="pagination"', false);
        $response->assertSeeInOrder([$category->name, $post[10]->title], false);
    }
}
