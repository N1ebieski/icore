<?php

namespace N1ebieski\ICore\Tests\Feature\Web;

use Tests\TestCase;
use N1ebieski\ICore\Models\Post;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Faker\Factory as Faker;
use Carbon\Carbon;

class TagTest extends TestCase
{
    use DatabaseTransactions;

    public function test_noexist_tag_show()
    {
        $response = $this->get(route('web.tag.post.show', ['dajskruiufi-2']));

        $response->assertStatus(404);
    }

    public function test_tag_show_paginate()
    {
        $tag = Faker::create()->word;

        $post = factory(Post::class, 50)->states(['active', 'with_user'])
            ->make()
            ->each(function($item, $key) use ($tag) {
                $item->published_at = Carbon::now()->addMinutes($key);
                $item->save();
                $item->tag($tag);
            });

        $response = $this->get(route('web.tag.post.show', [$post[0]->tags->first()->normalized, 'page' => 2]));

        $response->assertViewIs('icore::web.tag.show');
        $response->assertSee('role="navigation"');
        $response->assertSeeInOrder([$post[0]->tags->first()->name, $post[10]->title]);
    }
}
