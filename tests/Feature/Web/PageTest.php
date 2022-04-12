<?php

namespace N1ebieski\ICore\Tests\Feature\Web;

use Tests\TestCase;
use N1ebieski\ICore\Models\Page\Page;
use N1ebieski\ICore\Models\Comment\Comment;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PageTest extends TestCase
{
    use DatabaseTransactions;

    public function testNoexistPageShow()
    {
        $response = $this->get(route('web.page.show', ['dajskruiufi']));

        $response->assertStatus(404);
    }

    public function testPageShow()
    {
        $page = factory(Page::class)->states(['active', 'commentable', 'with_user'])->create();

        $response = $this->get(route('web.page.show', [$page->slug]));

        $response->assertSeeInOrder([$page->title, $page->content], false);
    }

    public function testPageShowPaginate()
    {
        $page = factory(Page::class)->states(['active', 'commentable', 'with_user'])->create();

        $comment = factory(Comment::class, 50)
            ->states(['active', 'with_user'])
            ->make()
            ->each(function ($item) use ($page) {
                $item->morph()->associate($page)->save();
            });

        $response = $this->get(route('web.page.show', [
            $page->slug,
            'page' => 2,
            'orderby' => 'created_at|asc'
        ]));

        $response->assertSee('class="pagination"', false);
        $response->assertSee($page->title, false);
        $response->assertSee($comment[30]->content, false);
    }
}
