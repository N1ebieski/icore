<?php

namespace N1ebieski\ICore\Tests\Feature\Web;

use Tests\TestCase;
use N1ebieski\ICore\Models\User;
use N1ebieski\ICore\Models\Post;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Carbon\Carbon;

class ArchiveTest extends TestCase
{
    use DatabaseTransactions;

    public function test_noexist_archive_show()
    {
        $response = $this->get(route('web.archive.post.show', ['month' => 99, 'year' => 99999]));

        $response->assertSessionHasErrors(['month', 'year']);
    }

    public function test_archive_show()
    {
        $response = $this->get(route('web.archive.post.show', ['month' => 12, 'year' => 2018]));

        $response->assertViewIs('icore::web.archive.post.show');
    }

    public function test_archive_show_paginate()
    {
        $post = factory(Post::class, 50)->states(['active', 'with_user'])
            ->make()
            ->each(function($item, $key) {
                $item->published_at = Carbon::createFromFormat('Y-m-d', '2018-12-23')->addMinutes($key);
                $item->save();
            });

        $response = $this->get(route('web.archive.post.show', ['month' => 12, 'year' => 2018, 'page' => 2]));

        $response->assertViewIs('icore::web.archive.post.show');
        $response->assertSee('class="pagination"');
        $response->assertSeeText($post[10]->title);
    }
}
