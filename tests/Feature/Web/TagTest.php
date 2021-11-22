<?php

namespace N1ebieski\ICore\Tests\Feature\Web;

use Carbon\Carbon;
use Tests\TestCase;
use Faker\Factory as Faker;
use N1ebieski\ICore\Models\Post;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class TagTest extends TestCase
{
    use DatabaseTransactions;

    public function testNoexistTagShow()
    {
        $response = $this->get(route('web.tag.post.show', ['dajskruiufi-2']));

        $response->assertStatus(404);
    }

    public function testTagShowPaginate()
    {
        $tag = Faker::create()->word;

        $post = factory(Post::class, 50)->states(['active', 'with_user'])
            ->make()
            ->each(function ($item, $key) use ($tag) {
                $item->published_at = Carbon::now()->addMinutes($key);
                $item->save();
                $item->tag($tag);
            });

        $response = $this->get(route('web.tag.post.show', [$post[0]->tags->first()->normalized, 'page' => 2]));

        $response->assertViewIs('icore::web.tag.post.show');
        $response->assertSee('class="pagination"');
        $response->assertSeeInOrder([$post[0]->tags->first()->name, $post[10]->title]);
    }
}
