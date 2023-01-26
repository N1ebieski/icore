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

namespace N1ebieski\ICore\Tests\Feature\Admin\Post;

use Carbon\Carbon;
use Tests\TestCase;
use N1ebieski\ICore\Models\Post;
use N1ebieski\ICore\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response as HttpResponse;
use N1ebieski\ICore\Models\PostLang\PostLang;
use N1ebieski\ICore\ValueObjects\Post\Status;
use N1ebieski\ICore\Models\Category\Post\Category;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CreatePostTest extends TestCase
{
    use DatabaseTransactions;

    public function testCreateAsGuest(): void
    {
        $response = $this->get(route('admin.post.create'));

        $response->assertRedirect(route('login'));
    }

    public function testCreateWithoutPermission(): void
    {
        /** @var User */
        $user = User::makeFactory()->create();

        Auth::login($user);

        $response = $this->get(route('admin.post.create'));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testCreate(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        Auth::login($user);

        $response = $this->get(route('admin.post.create'));

        $response->assertOk()
            ->assertViewIs('icore::admin.post.create')
            ->assertSee(route('admin.post.store'), false);
    }

    public function testStoreAsGuest(): void
    {
        $response = $this->post(route('admin.post.store'));

        $response->assertRedirect(route('login'));
    }

    public function testStoreWithoutPermission(): void
    {
        /** @var User */
        $user = User::makeFactory()->create();

        Auth::login($user);

        $response = $this->post(route('admin.post.store'));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testStoreValidationFail(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        Auth::login($user);

        $response = $this->post(route('admin.post.store'), [
            'title' => 'Hdjshdjshdjshdjsds',
            'content_html' => 'Ten post został zaktualizowany.'
        ]);

        $response->assertSessionHasErrors(['categories', 'status', 'date_published_at', 'time_published_at']);
    }

    public function testStore(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        /** @var Category */
        $category = Category::makeFactory()->active()->create();

        Auth::login($user);

        $response = $this->post(route('admin.post.store'), [
            'categories' => [$category->id],
            'title' => 'Post dodany.',
            'content_html' => 'Ten post został dodany.',
            'tags' => 'test1, test2, test3',
            'status' => Status::ACTIVE,
            'date_published_at' => Carbon::now()->format('Y-m-d'),
            'time_published_at' => Carbon::now()->format('H:i')
        ]);

        $response->assertSessionHas('success');

        /** @var PostLang|null */
        $postLang = PostLang::where('title', 'Post dodany.')->first();

        $this->assertTrue($postLang?->exists());

        $response->assertRedirect(route('admin.post.index', [
            'filter' => [
                'search' => "id:\"{$postLang->post->id}\""
            ]
        ]));

        $this->assertDatabaseHas('categories_models', [
            'model_id' => $postLang->post->id,
            'model_type' => $postLang->post->getMorphClass(),
            'category_id' => $category->id
        ]);

        $this->assertDatabaseHas('tags_models', [
            'model_id' => $postLang->post->id,
            'model_type' => $postLang->post->getMorphClass(),
        ]);
    }
}
