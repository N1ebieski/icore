<?php

namespace N1ebieski\ICore\Tests\Feature\Admin;

use Carbon\Carbon;
use Tests\TestCase;
use N1ebieski\ICore\Models\Post;
use N1ebieski\ICore\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response as HttpResponse;
use N1ebieski\ICore\Models\Category\Post\Category;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PostTest extends TestCase
{
    use DatabaseTransactions;

    public function testPostIndexAsGuest()
    {
        $response = $this->get(route('admin.post.index'));

        $response->assertRedirect(route('login'));
    }

    public function testPostIndexWithoutPermission()
    {
        $user = User::makeFactory()->create();

        Auth::login($user);

        $response = $this->get(route('admin.post.index'));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testPostIndexPaginate()
    {
        $user = User::makeFactory()->admin()->create();

        $post = Post::makeFactory()->count(50)->active()->publish()->withUser()->create();

        Auth::login($user);

        $response = $this->get(route('admin.post.index', [
            'page' => 2,
            'filter' => [
                'orderby' => 'created_at|asc'
            ]
        ]));

        $response->assertViewIs('icore::admin.post.index');
        $response->assertSee('class="pagination"', false);
        $response->assertSeeInOrder([$post[30]->title, $post[30]->shortContent], false);
    }

    public function testPostEditAsGuest()
    {
        $response = $this->get(route('admin.post.edit', [43]));

        $response->assertRedirect(route('login'));
    }

    public function testPostEditWithoutPermission()
    {
        $user = User::makeFactory()->create();

        $post = Post::makeFactory()->active()->publish()->withUser()->create();

        Auth::login($user);

        $response = $this->get(route('admin.post.edit', [$post->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testNoexistPostEdit()
    {
        $user = User::makeFactory()->admin()->create();

        Auth::login($user);

        $response = $this->get(route('admin.post.edit', [2327382]));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testPostEdit()
    {
        $user = User::makeFactory()->admin()->create();

        $post = Post::makeFactory()->active()->publish()->withUser()->create();

        Auth::login($user);

        $response = $this->get(route('admin.post.edit', [$post->id]));

        $response->assertOk()->assertJsonStructure(['view']);

        $this->assertStringContainsString($post->content, $response->getData()->view);
        $this->assertStringContainsString(route('admin.post.update', [$post->id]), $response->getData()->view);
    }

    public function testPostUpdateAsGuest()
    {
        $response = $this->put(route('admin.post.update', [43]));

        $response->assertRedirect(route('login'));
    }

    public function testPostUpdateWithoutPermission()
    {
        $user = User::makeFactory()->create();

        $post = Post::makeFactory()->active()->publish()->withUser()->create();

        Auth::login($user);

        $response = $this->put(route('admin.post.update', [$post->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testNoexistPostUpdate()
    {
        $user = User::makeFactory()->admin()->create();

        Auth::login($user);

        $response = $this->put(route('admin.post.update', [2327382]));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testPostUpdateValidationFail()
    {
        $user = User::makeFactory()->admin()->create();

        $post = Post::makeFactory()->active()->publish()->withUser()->create();

        Auth::login($user);

        $response = $this->put(route('admin.post.update', [$post->id]), [
            'title' => '',
            'content_html' => 'Ten post został zaktualizowany.'
        ]);

        $response->assertSessionHasErrors(['title']);
    }

    public function testPostUpdate()
    {
        $user = User::makeFactory()->admin()->create();

        $post = Post::makeFactory()->active()->publish()->withUser()->create();

        Auth::login($user);

        $response = $this->put(route('admin.post.update', [$post->id]), [
            'title' => 'Post zaktualizowany.',
            'content_html' => 'Ten post został zaktualizowany.'
        ]);

        $response->assertOk()->assertJsonStructure(['view']);
        $this->assertStringContainsString('Ten post został zaktualizowany.', $response->getData()->view);

        $this->assertDatabaseHas('posts', [
            'content' => 'Ten post został zaktualizowany.',
            'title' => 'Post zaktualizowany.',
        ]);
    }

    public function testPostEditFullAsGuest()
    {
        $response = $this->get(route('admin.post.edit_full', [43]));

        $response->assertRedirect(route('login'));
    }

    public function testPostEditFullWithoutPermission()
    {
        $user = User::makeFactory()->create();

        $post = Post::makeFactory()->active()->publish()->withUser()->create();

        Auth::login($user);

        $response = $this->get(route('admin.post.edit_full', [$post->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testNoexistPostEditFull()
    {
        $user = User::makeFactory()->admin()->create();

        Auth::login($user);

        $response = $this->get(route('admin.post.edit_full', [2327382]));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testPostEditFull()
    {
        $user = User::makeFactory()->admin()->create();

        $post = Post::makeFactory()->active()->publish()->withUser()->create();

        Auth::login($user);

        $response = $this->get(route('admin.post.edit_full', [$post->id]));

        $response->assertOk()->assertViewIs('icore::admin.post.edit_full');
        $response->assertSeeInOrder([$post->title, $post->content], false);
        $response->assertSee(route('admin.post.update_full', [$post->id]), false);
    }

    public function testPostUpdateFullAsGuest()
    {
        $response = $this->put(route('admin.post.update_full', [43]));

        $response->assertRedirect(route('login'));
    }

    public function testPostUpdateFullWithoutPermission()
    {
        $user = User::makeFactory()->create();

        $post = Post::makeFactory()->active()->publish()->withUser()->create();

        Auth::login($user);

        $response = $this->put(route('admin.post.update_full', [$post->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testNoexistPostUpdateFull()
    {
        $user = User::makeFactory()->admin()->create();

        Auth::login($user);

        $response = $this->put(route('admin.post.update_full', [2327382]));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testPostUpdateFullValidationFail()
    {
        $user = User::makeFactory()->admin()->create();

        $post = Post::makeFactory()->active()->publish()->withUser()->create();

        Auth::login($user);

        $response = $this->put(route('admin.post.update_full', [$post->id]), [
            'title' => 'Hdjshdjshdjshdjsds',
            'content_html' => 'Ten post został zaktualizowany.'
        ]);

        $response->assertSessionHasErrors(['categories', 'status', 'time_published_at', 'date_published_at']);
    }

    public function testPostUpdateFull()
    {
        $user = User::makeFactory()->admin()->create();

        $post = Post::makeFactory()->active()->publish()->withUser()->create();

        $category = Category::makeFactory()->active()->create();

        Auth::login($user);

        $response = $this->put(route('admin.post.update_full', [$post->id]), [
            'categories' => [$category->id],
            'user' => $user->id,
            'title' => 'Post zaktualizowany.',
            'content_html' => 'Ten post został zaktualizowany.',
            'tags' => 'test1, test2, test3',
            'status' => 1,
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

    public function testPostUpdateStatusAsGuest()
    {
        $response = $this->patch(route('admin.post.update_status', [43]));

        $response->assertRedirect(route('login'));
    }

    public function testPostUpdateStatusWithoutPermission()
    {
        $user = User::makeFactory()->create();

        $post = Post::makeFactory()->active()->publish()->withUser()->create();

        Auth::login($user);

        $response = $this->patch(route('admin.post.update_status', [$post->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testNoexistPostUpdateStatus()
    {
        $user = User::makeFactory()->admin()->create();

        Auth::login($user);

        $response = $this->patch(route('admin.post.update_status', [2327382]));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testPostUpdateStatusValidationFail()
    {
        $user = User::makeFactory()->admin()->create();

        $post = Post::makeFactory()->active()->publish()->withUser()->create();

        Auth::login($user);

        $response = $this->patch(route('admin.post.update_status', [$post->id]), [
            'status' => 323,
        ]);

        $response->assertSessionHasErrors(['status']);
    }

    public function testPostUpdateStatus()
    {
        $user = User::makeFactory()->admin()->create();

        $post = Post::makeFactory()->active()->publish()->withUser()->create();

        Auth::login($user);

        $response = $this->patch(route('admin.post.update_status', [$post->id]), [
            'status' => 0,
        ]);

        $response->assertOk()->assertJsonStructure(['view']);

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'status' => 0,
        ]);
    }

    public function testPostDestroyAsGuest()
    {
        $response = $this->delete(route('admin.post.destroy', [43]));

        $response->assertRedirect(route('login'));
    }

    public function testPostDestroyWithoutPermission()
    {
        $user = User::makeFactory()->create();

        $post = Post::makeFactory()->active()->publish()->withUser()->create();

        Auth::login($user);

        $response = $this->delete(route('admin.post.destroy', [$post->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testNoexistPostDestroy()
    {
        $user = User::makeFactory()->admin()->create();

        Auth::login($user);

        $response = $this->delete(route('admin.post.destroy', [2327382]));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testPostDestroy()
    {
        $user = User::makeFactory()->admin()->create();

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

    public function testPostDestroyGlobalAsGuest()
    {
        $response = $this->delete(route('admin.post.destroy_global'), []);

        $response->assertRedirect(route('login'));
    }

    public function testPostDestroyGlobalWithoutPermission()
    {
        $user = User::makeFactory()->create();

        Auth::login($user);

        $response = $this->delete(route('admin.post.destroy_global'), []);

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testPostDestroyGlobalValidationFail()
    {
        $user = User::makeFactory()->admin()->create();

        Auth::login($user);

        $response = $this->delete(route('admin.post.destroy_global', [
            'dasdas',
            'dada'
        ]));

        $response->assertSessionHasErrors(['select']);
    }

    public function testPostDestroyGlobal()
    {
        $user = User::makeFactory()->admin()->create();

        $post = Post::makeFactory()->count(10)->active()->publish()->withUser()->create();

        Auth::login($user);

        $this->get(route('admin.post.index'));

        $select = collect($post)->pluck('id')->take(5)->toArray();

        $response = $this->delete(route('admin.post.destroy_global'), [
            'select' => $select,
        ]);

        $response->assertRedirect(route('admin.post.index'));
        $response->assertSessionHas('success');

        $deleted = Post::whereIn('id', $select)->get();

        $this->assertTrue($deleted->count() === 0);
    }

    public function testPostCreateAsGuest()
    {
        $response = $this->get(route('admin.post.create'));

        $response->assertRedirect(route('login'));
    }

    public function testPostCreateWithoutPermission()
    {
        $user = User::makeFactory()->create();

        Auth::login($user);

        $response = $this->get(route('admin.post.create'));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testPostCreate()
    {
        $user = User::makeFactory()->admin()->create();

        Auth::login($user);

        $response = $this->get(route('admin.post.create'));

        $response->assertOk()->assertViewIs('icore::admin.post.create');
        $response->assertSee(route('admin.post.store'), false);
    }

    public function testPostStoreAsGuest()
    {
        $response = $this->post(route('admin.post.store'));

        $response->assertRedirect(route('login'));
    }

    public function testPostStoreWithoutPermission()
    {
        $user = User::makeFactory()->create();

        Auth::login($user);

        $response = $this->post(route('admin.post.store'));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testPostStoreValidationFail()
    {
        $user = User::makeFactory()->admin()->create();

        Auth::login($user);

        $response = $this->post(route('admin.post.store'), [
            'title' => 'Hdjshdjshdjshdjsds',
            'content_html' => 'Ten post został zaktualizowany.'
        ]);

        $response->assertSessionHasErrors(['categories', 'status', 'date_published_at', 'time_published_at']);
    }

    public function testPostStore()
    {
        $user = User::makeFactory()->admin()->create();

        $category = Category::makeFactory()->active()->create();

        Auth::login($user);

        $response = $this->post(route('admin.post.store'), [
            'categories' => [$category->id],
            'title' => 'Post dodany.',
            'content_html' => 'Ten post został dodany.',
            'tags' => 'test1, test2, test3',
            'status' => 1,
            'date_published_at' => Carbon::now()->format('Y-m-d'),
            'time_published_at' => Carbon::now()->format('H:i')
        ]);

        $response->assertRedirect(route('admin.post.index'));
        $response->assertSessionHas('success');

        $post = Post::where([
            ['content', 'Ten post został dodany.'],
            ['title', 'Post dodany.']
        ])->first();

        $this->assertTrue($post->exists());

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
}
