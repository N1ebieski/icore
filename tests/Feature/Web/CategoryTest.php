<?php

namespace N1ebieski\ICore\Tests\Feature\Web;

use Carbon\Carbon;
use Tests\TestCase;
use N1ebieski\ICore\Models\Post;
use N1ebieski\ICore\Models\User;
use N1ebieski\ICore\Models\Category\Post\Category;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CategoryTest extends TestCase
{
    use DatabaseTransactions;

    public function testNoexistCategoryShow()
    {
        $response = $this->get(route('web.category.post.show', ['dajskruiufi']));

        $response->assertStatus(404);
    }

    public function testCategoryShow()
    {
        $category = factory(Category::class)->states('active')->create();

        $response = $this->get(route('web.category.post.show', [$category->slug]));

        $response->assertViewIs('icore::web.category.post.show');
    }

    public function testCategoryShowPaginate()
    {
        $category = factory(Category::class)->states('active')->create();

        $post = factory(Post::class, 50)->states(['active', 'with_user'])
            ->make()
            ->each(function ($item, $key) use ($category) {
                $item->published_at = Carbon::now()->addMinutes($key);
                $item->save();
                $item->categories()->attach($category);
            });

        $response = $this->get(route('web.category.post.show', [$category->slug, 'page' => 2]));

        $response->assertSee('class="pagination"', false);
        $response->assertSeeInOrder([$category->name, $post[10]->title], false);
    }
}
