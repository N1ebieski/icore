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

namespace N1ebieski\ICore\Tests\Feature\Web\Page;

use Tests\TestCase;
use N1ebieski\ICore\Models\Page\Page;
use N1ebieski\ICore\Models\Comment\Comment;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ShowPageTest extends TestCase
{
    use DatabaseTransactions;

    public function testShowNoExist(): void
    {
        $response = $this->get(route('web.page.show', ['dajskruiufi']));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testShow(): void
    {
        /** @var Page */
        $page = Page::makeFactory()->active()->commentable()->withUser()->create();

        $response = $this->get(route('web.page.show', [$page->slug]));

        $response->assertSeeInOrder([$page->title, $page->content], false);
    }

    public function testShowPaginate(): void
    {
        /** @var Page */
        $page = Page::makeFactory()->active()->commentable()->withUser()->create();

        /** @var Collection<Comment>|array<Comment> */
        $comments = Comment::makeFactory()->count(50)->active()->withUser()->for($page, 'morph')->create();

        $response = $this->get(route('web.page.show', [
            $page->slug,
            'page' => 2,
            'filter' => [
                'orderby' => 'created_at|asc'
            ]
        ]));

        $response->assertSee('class="pagination"', false)
            ->assertSee($page->title, false)
            ->assertSee($comments[30]->content, false);
    }
}
