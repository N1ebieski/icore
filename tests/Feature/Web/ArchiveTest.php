<?php

namespace N1ebieski\ICore\Tests\Feature\Web;

use Carbon\Carbon;
use Tests\TestCase;
use N1ebieski\ICore\Models\Post;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ArchiveTest extends TestCase
{
    use DatabaseTransactions;

    public function testNoexistArchiveShow()
    {
        $response = $this->get(route('web.archive.post.show', ['month' => 99, 'year' => 99999]));

        $response->assertSessionHasErrors(['month', 'year']);
    }

    public function testArchiveShow()
    {
        $response = $this->get(route('web.archive.post.show', ['month' => 12, 'year' => 2018]));

        $response->assertViewIs('icore::web.archive.post.show');
    }

    public function testArchiveShowPaginate()
    {
        $post = Post::makeFactory()->count(50)->active()->withUser()
            ->sequence(function ($sequence) {
                return [
                    'published_at' => Carbon::createFromFormat('Y-m-d', '2018-12-23')->addMinutes($sequence->index)
                ];
            })
            ->create();

        $response = $this->get(route('web.archive.post.show', ['month' => 12, 'year' => 2018, 'page' => 2]));

        $response->assertViewIs('icore::web.archive.post.show');
        $response->assertSee('class="pagination"', false);
        $response->assertSeeText($post[10]->title, false);
    }
}
