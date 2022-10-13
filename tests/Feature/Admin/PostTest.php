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

namespace N1ebieski\ICore\Tests\Feature\Admin;

use Carbon\Carbon;
use Tests\TestCase;
use N1ebieski\ICore\Models\Post;
use N1ebieski\ICore\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Response as HttpResponse;
use N1ebieski\ICore\ValueObjects\Post\Status;
use N1ebieski\ICore\Models\Category\Post\Category;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PostTest extends TestCase
{
    use DatabaseTransactions;

    public function testPostIndexAsGuest(): void
    {
        $response = $this->get(route('admin.post.index'));

        $response->assertRedirect(route('login'));
    }

    public function testPostIndexWithoutPermission(): void
    {
        /** @var User */
        $user = User::makeFactory()->create();

        Auth::login($user);

        $response = $this->get(route('admin.post.index'));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testPostIndexPaginate(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        /** @var Collection<Post>|array<Post> */
        $posts = Post::makeFactory()->count(50)
            ->sequence(function (Sequence $sequence) {
                return [
                    'created_at' => Carbon::now()->addSeconds($sequence->index)
                ];
            })
            ->active()
            ->publish()
            ->withUser()
            ->create();

        Auth::login($user);

        $response = $this->get(route('admin.post.index', [
            'page' => 2,
            'filter' => [
                'orderby' => 'created_at|asc'
            ]
        ]));

        $response->assertViewIs('icore::admin.post.index')
            ->assertSee('class="pagination"', false)
            ->assertSeeInOrder([$posts[30]->title, $posts[30]->shortContent], false);
    }

    public function testPostEditAsGuest(): void
    {
        $response = $this->get(route('admin.post.edit', [43]));

        $response->assertRedirect(route('login'));
    }

    public function testPostEditWithoutPermission(): void
    {
        /** @var User */
        $user = User::makeFactory()->create();

        /** @var Post */
        $post = Post::makeFactory()->active()->publish()->withUser()->create();

        Auth::login($user);

        $response = $this->get(route('admin.post.edit', [$post->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testNoexistPostEdit(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        Auth::login($user);

        $response = $this->get(route('admin.post.edit', [2327382]));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testPostEdit(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        /** @var Post */
        $post = Post::makeFactory()->active()->publish()->withUser()->create();

        Auth::login($user);

        $response = $this->get(route('admin.post.edit', [$post->id]));

        $response->assertOk()->assertJsonStructure(['view']);

        /** @var JsonResponse */
        $baseResponse = $response->baseResponse;

        $this->assertStringContainsString(
            $post->content ?? '',
            $baseResponse->getData()->view
        );
        $this->assertStringContainsString(
            route('admin.post.update', [$post->id]),
            $baseResponse->getData()->view
        );
    }

    public function testPostUpdateAsGuest(): void
    {
        $response = $this->put(route('admin.post.update', [43]));

        $response->assertRedirect(route('login'));
    }

    public function testPostUpdateWithoutPermission(): void
    {
        /** @var User */
        $user = User::makeFactory()->create();

        /** @var Post */
        $post = Post::makeFactory()->active()->publish()->withUser()->create();

        Auth::login($user);

        $response = $this->put(route('admin.post.update', [$post->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testNoexistPostUpdate(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        Auth::login($user);

        $response = $this->put(route('admin.post.update', [2327382]));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testPostUpdateValidationFail(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        /** @var Post */
        $post = Post::makeFactory()->active()->publish()->withUser()->create();

        Auth::login($user);

        $response = $this->put(route('admin.post.update', [$post->id]), [
            'title' => '',
            'content_html' => 'Ten post został zaktualizowany.'
        ]);

        $response->assertSessionHasErrors(['title']);
    }

    public function testPostUpdate(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        /** @var Post */
        $post = Post::makeFactory()->active()->publish()->withUser()->create();

        Auth::login($user);

        $response = $this->put(route('admin.post.update', [$post->id]), [
            'title' => 'Post zaktualizowany.',
            'content_html' => 'Ten post został zaktualizowany.'
        ]);

        $response->assertOk()->assertJsonStructure(['view']);

        /** @var JsonResponse */
        $baseResponse = $response->baseResponse;

        $this->assertStringContainsString(
            'Ten post został zaktualizowany.',
            $baseResponse->getData()->view
        );

        $this->assertDatabaseHas('posts', [
            'content' => 'Ten post został zaktualizowany.',
            'title' => 'Post zaktualizowany.',
        ]);
    }

    public function testPostEditFullAsGuest(): void
    {
        $response = $this->get(route('admin.post.edit_full', [43]));

        $response->assertRedirect(route('login'));
    }

    public function testPostEditFullWithoutPermission(): void
    {
        /** @var User */
        $user = User::makeFactory()->create();

        /** @var Post */
        $post = Post::makeFactory()->active()->publish()->withUser()->create();

        Auth::login($user);

        $response = $this->get(route('admin.post.edit_full', [$post->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testNoexistPostEditFull(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        Auth::login($user);

        $response = $this->get(route('admin.post.edit_full', [2327382]));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testPostEditFull(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        /** @var Post */
        $post = Post::makeFactory()->active()->publish()->withUser()->create();

        Auth::login($user);

        $response = $this->get(route('admin.post.edit_full', [$post->id]));

        $response->assertOk()
            ->assertViewIs('icore::admin.post.edit_full')
            ->assertSeeInOrder([$post->title, $post->content], false)
            ->assertSee(route('admin.post.update_full', [$post->id]), false);
    }

    public function testPostUpdateFullAsGuest(): void
    {
        $response = $this->put(route('admin.post.update_full', [43]));

        $response->assertRedirect(route('login'));
    }

    public function testPostUpdateFullWithoutPermission(): void
    {
        /** @var User */
        $user = User::makeFactory()->create();

        /** @var Post */
        $post = Post::makeFactory()->active()->publish()->withUser()->create();

        Auth::login($user);

        $response = $this->put(route('admin.post.update_full', [$post->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testNoexistPostUpdateFull(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        Auth::login($user);

        $response = $this->put(route('admin.post.update_full', [2327382]));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testPostUpdateFullValidationFail(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        /** @var Post */
        $post = Post::makeFactory()->active()->publish()->withUser()->create();

        Auth::login($user);

        $response = $this->put(route('admin.post.update_full', [$post->id]), [
            'title' => 'Hdjshdjshdjshdjsds',
            'content_html' => 'Ten post został zaktualizowany.'
        ]);

        $response->assertSessionHasErrors(['categories', 'status', 'time_published_at', 'date_published_at']);
    }

    public function testPostUpdateFull(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        /** @var Post */
        $post = Post::makeFactory()->active()->publish()->withUser()->create();

        /** @var Category */
        $category = Category::makeFactory()->active()->create();

        Auth::login($user);

        $response = $this->put(route('admin.post.update_full', [$post->id]), [
            'categories' => [$category->id],
            'user' => $user->id,
            'title' => 'Post zaktualizowany.',
            'content_html' => 'Ten post został zaktualizowany.',
            'tags' => 'test1, test2, test3',
            'status' => Status::ACTIVE,
            'date_published_at' => Carbon::now()->format('Y-m-d'),
            'time_published_at' => Carbon::now()->format('H:i')
        ]);

        $response->assertRedirect(route('admin.post.edit_full', [$post->id]));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('posts', [
            'content' => 'Ten post został zaktualizowany.',
            'title' => 'Post zaktualizowany.',
        ]);

        $this->assertDatabaseHas('categories_models', [
            'model_id' => $post->id,
            'model_type' => 'N1ebieski\\ICore\\Models\\Post',
            'category_id' => $category->id
        ]);

        $this->assertDatabaseHas('tags_models', [
            'model_id' => $post->id,
            'model_type' => 'N1ebieski\\ICore\\Models\\Post',
        ]);
    }

    public function testPostUpdateStatusAsGuest(): void
    {
        $response = $this->patch(route('admin.post.update_status', [43]));

        $response->assertRedirect(route('login'));
    }

    public function testPostUpdateStatusWithoutPermission(): void
    {
        /** @var User */
        $user = User::makeFactory()->create();

        /** @var Post */
        $post = Post::makeFactory()->active()->publish()->withUser()->create();

        Auth::login($user);

        $response = $this->patch(route('admin.post.update_status', [$post->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testNoexistPostUpdateStatus(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        Auth::login($user);

        $response = $this->patch(route('admin.post.update_status', [2327382]));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testPostUpdateStatusValidationFail(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        /** @var Post */
        $post = Post::makeFactory()->active()->publish()->withUser()->create();

        Auth::login($user);

        $response = $this->patch(route('admin.post.update_status', [$post->id]), [
            'status' => 323,
        ]);

        $response->assertSessionHasErrors(['status']);
    }

    public function testPostUpdateStatus(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        /** @var Post */
        $post = Post::makeFactory()->active()->publish()->withUser()->create();

        Auth::login($user);

        $response = $this->patch(route('admin.post.update_status', [$post->id]), [
            'status' => Status::INACTIVE,
        ]);

        $response->assertOk()->assertJsonStructure(['view']);

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'status' => Status::INACTIVE,
        ]);
    }

    public function testPostDestroyAsGuest(): void
    {
        $response = $this->delete(route('admin.post.destroy', [43]));

        $response->assertRedirect(route('login'));
    }

    public function testPostDestroyWithoutPermission(): void
    {
        /** @var User */
        $user = User::makeFactory()->create();

        /** @var Post */
        $post = Post::makeFactory()->active()->publish()->withUser()->create();

        Auth::login($user);

        $response = $this->delete(route('admin.post.destroy', [$post->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testNoexistPostDestroy(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        Auth::login($user);

        $response = $this->delete(route('admin.post.destroy', [2327382]));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testPostDestroy(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        /** @var Post */
        $post = Post::makeFactory()->active()->publish()->withUser()->create();

        Auth::login($user);

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
        ]);

        $response = $this->delete(route('admin.post.destroy', [$post->id]), []);

        $response->assertOk();

        $this->assertDatabaseMissing('posts', [
            'id' => $post->id,
        ]);
    }

    public function testPostDestroyGlobalAsGuest(): void
    {
        $response = $this->delete(route('admin.post.destroy_global'), []);

        $response->assertRedirect(route('login'));
    }

    public function testPostDestroyGlobalWithoutPermission(): void
    {
        /** @var User */
        $user = User::makeFactory()->create();

        Auth::login($user);

        $response = $this->delete(route('admin.post.destroy_global'), []);

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testPostDestroyGlobalValidationFail(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        Auth::login($user);

        $response = $this->delete(route('admin.post.destroy_global', [
            'dasdas',
            'dada'
        ]));

        $response->assertSessionHasErrors(['select']);
    }

    public function testPostDestroyGlobal(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        /** @var Collection<Post> */
        $posts = Post::makeFactory()->count(10)->active()->publish()->withUser()->create();

        Auth::login($user);

        $this->get(route('admin.post.index'));

        $select = collect($posts)->pluck('id')->take(5)->toArray();

        $response = $this->delete(route('admin.post.destroy_global'), [
            'select' => $select,
        ]);

        $response->assertRedirect(route('admin.post.index'));
        $response->assertSessionHas('success');

        $deleted = Post::whereIn('id', $select)->get();

        $this->assertTrue($deleted->count() === 0);
    }

    public function testPostCreateAsGuest(): void
    {
        $response = $this->get(route('admin.post.create'));

        $response->assertRedirect(route('login'));
    }

    public function testPostCreateWithoutPermission(): void
    {
        /** @var User */
        $user = User::makeFactory()->create();

        Auth::login($user);

        $response = $this->get(route('admin.post.create'));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testPostCreate(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        Auth::login($user);

        $response = $this->get(route('admin.post.create'));

        $response->assertOk()
            ->assertViewIs('icore::admin.post.create')
            ->assertSee(route('admin.post.store'), false);
    }

    public function testPostStoreAsGuest(): void
    {
        $response = $this->post(route('admin.post.store'));

        $response->assertRedirect(route('login'));
    }

    public function testPostStoreWithoutPermission(): void
    {
        /** @var User */
        $user = User::makeFactory()->create();

        Auth::login($user);

        $response = $this->post(route('admin.post.store'));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testPostStoreValidationFail(): void
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

    public function testPostStore(): void
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

        /** @var Post|null */
        $post = Post::where([
            ['content', 'Ten post został dodany.'],
            ['title', 'Post dodany.']
        ])->first();

        $this->assertTrue($post?->exists());

        $response->assertRedirect(route('admin.post.index', [
            'filter' => [
                'search' => "id:\"{$post?->id}\""
            ]
        ]));

        $this->assertDatabaseHas('categories_models', [
            'model_id' => $post?->id,
            'model_type' => $post?->getMorphClass(),
            'category_id' => $category->id
        ]);

        $this->assertDatabaseHas('tags_models', [
            'model_id' => $post?->id,
            'model_type' => $post?->getMorphClass(),
        ]);
    }
}
