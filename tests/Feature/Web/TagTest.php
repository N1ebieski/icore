<?php

namespace N1ebieski\ICore\Tests\Feature\Web;

use Carbon\Carbon;
use Tests\TestCase;
use N1ebieski\ICore\Models\Post;
use N1ebieski\ICore\Models\Tag\Post\Tag;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class TagTest extends TestCase
{
    use DatabaseTransactions;

    public function testNoexistTagShow()
    {
        $response = $this->get(route('web.tag.post.show', ['dajskruiufi-2']));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testTagShowPaginate()
    {
        $posts = Post::makeFactory()->count(50)->active()->withUser()
            ->sequence(function ($sequence) {
                return [
                    'published_at' => Carbon::now()->addMinutes($sequence->index)
                ];
            })
            ->create();

        $tag = Tag::makeFactory()->hasAttached($posts, [], 'morphs')->create();

        $response = $this->get(route('web.tag.post.show', [$tag->normalized, 'page' => 2]));

        $response->assertViewIs('icore::web.tag.post.show');
        $response->assertSee('class="pagination"', false);
        $response->assertSeeInOrder([$tag->name, $posts[10]->title], false);
    }
}
