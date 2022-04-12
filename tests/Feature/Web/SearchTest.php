<?php

namespace N1ebieski\ICore\Tests\Feature\Web;

use Tests\TestCase;
use N1ebieski\ICore\Models\Post;
use Illuminate\Support\Facades\DB;
use N1ebieski\ICore\Models\Tag\Tag;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SearchTest extends TestCase
{
    use DatabaseTransactions;

    // public function testSearchAutocompleteValidationFail()
    // {
    //     $response = $this->get(route('web.search.autocomplete'), [
    //         'search' => 'fd'
    //     ]);

    //     $response->assertSessionHasErrors(['search']);
    // }

    // public function testSearchAutocomplete()
    // {
    //     $tag = Tag::create([
    //         'name' => 'Ędward Ącki'
    //     ]);

    //     // Hook z koniecznosci. Wyszukiwanie odbywa się przez AGAINST MATCH po indeksie,
    //     // a DatabaseTransactions nie indeksuje ostatnio dodanego modelu.
    //     DB::statement('OPTIMIZE TABLE tags');

    //     $response = $this->get(route('web.search.autocomplete', [
    //         'search' => 'ąck'
    //     ]));

    //     $response->assertOk();
    //     $this->assertStringContainsString($tag->name, $response->getData()[0]->name);

    //     DB::statement('DELETE FROM `tags` WHERE `tag_id` > 0');
    // }

    public function testSearchValidationFail()
    {
        $response = $this->get(route('web.search.index'), [
            'search' => 'fd'
        ]);

        $response->assertSessionHasErrors(['search', 'source']);
    }

    public function testSearch()
    {
        $post = factory(Post::class)->states(['active', 'publish', 'with_user'])->create([
            'title' => 'Post należący do testów w phpunit'
        ]);

        // Hook z koniecznosci. Wyszukiwanie odbywa się przez AGAINST MATCH po indeksie,
        // a DatabaseTransactions nie indeksuje ostatnio dodanego modelu.
        DB::statement('OPTIMIZE TABLE posts');

        $response = $this->followingRedirects()->get(route('web.search.index', [
            'search' => 'należący do',
            'source' => 'post'
        ]));

        $response->assertViewIs('icore::web.post.search');
        $response->assertSeeInOrder([$post->title, $post->content_html], false);

        DB::statement('DELETE FROM `posts` WHERE `id` > 0');
    }
}
