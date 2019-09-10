<?php

namespace N1ebieski\ICore\Tests\Feature\Web;

use Tests\TestCase;
use N1ebieski\ICore\Models\Page\Page;
use N1ebieski\ICore\Models\Comment\Comment;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PageTest extends TestCase
{
    use DatabaseTransactions;

    public function test_noexist_page_show()
    {
        $response = $this->get(route('web.page.show', ['dajskruiufi']));

        $response->assertStatus(404);
    }

    public function test_page_show()
    {
        $page = factory(Page::class)->states(['active', 'commentable', 'with_user'])->create();

        $response = $this->get(route('web.page.show', [$page->slug]));

        $response->assertSeeInOrder([$page->title, $page->content]);
    }

    public function test_page_show_paginate()
    {
        $page = factory(Page::class)->states(['active', 'commentable', 'with_user'])->create();

        $comment = factory(Comment::class, 50)
            ->states(['active', 'with_user'])
            ->make()
            ->each(function($item) use ($page) {
                $item->morph()->associate($page)->save();
            });

        $response = $this->get(route('web.page.show', [
            $page->slug,
            'page' => 2,
            'orderby' => 'created_at|asc'
        ]));

        $response->assertSee('role="navigation"');
        $response->assertSee($page->title);
        $response->assertSee($comment[30]->content);
    }
}
