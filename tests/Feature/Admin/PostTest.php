<?php

namespace N1ebieski\ICore\Tests\Feature\Admin;

use Carbon\Carbon;
use Tests\TestCase;
use N1ebieski\ICore\Models\Post;
use N1ebieski\ICore\Models\User;
use Illuminate\Support\Facades\Auth;
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
        $user = User::factory()->create();

        Auth::login($user);

        $response = $this->get(route('admin.post.index'));

        $response->assertStatus(403);
    }

    public function testPostIndexPaginate()
    {
        $user = User::factory()->admin()->create();

        $post = Post::factory()->count(50)->active()->publish()->withUser()->create();

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
        $user = User::factory()->create();

        $post = Post::factory()->active()->publish()->withUser()->create();

        Auth::login($user);

        $response = $this->get(route('admin.post.edit', [$post->id]));

        $response->assertStatus(403);
    }

    public function testNoexistPostEdit()
    {
        $user = User::factory()->admin()->create();

        Auth::login($user);

        $response = $this->get(route('admin.post.edit', [2327382]));

        $response->assertStatus(404);
    }

    public function testPostEdit()
    {
        $user = User::factory()->admin()->create();

        $post = Post::factory()->active()->publish()->withUser()->create();

        Auth::login($user);

        $response = $this->get(route('admin.post.edit', [$post->id]));

        $response->assertOk()->assertJsonStructure(['success', 'view']);

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
        $user = User::factory()->create();

        $post = Post::factory()->active()->publish()->withUser()->create();

        Auth::login($user);

        $response = $this->put(route('admin.post.update', [$post->id]));

        $response->assertStatus(403);
    }

    public function testNoexistPostUpdate()
    {
        $user = User::factory()->admin()->create();

        Auth::login($user);

        $response = $this->put(route('admin.post.update', [2327382]));

        $response->assertStatus(404);
    }

    public function testPostUpdateValidationFail()
    {
        $user = User::factory()->admin()->create();

        $post = Post::factory()->active()->publish()->withUser()->create();

        Auth::login($user);

        $response = $this->put(route('admin.post.update', [$post->id]), [
            'title' => '',
            'content_html' => 'Ten post został zaktualizowany.'
        ]);

        $response->assertSessionHasErrors(['title']);

        $this->assertTrue(Auth::check());
    }

    public function testPostUpdate()
    {
        $user = User::factory()->admin()->create();

        $post = Post::factory()->active()->publish()->withUser()->create();

        Auth::login($user);

        $response = $this->put(route('admin.post.update', [$post->id]), [
            'title' => 'Post zaktualizowany.',
            'content_html' => 'Ten post został zaktualizowany.'
        ]);

        $response->assertOk()->assertJsonStructure(['success', 'view']);
        $this->assertStringContainsString('Ten post został zaktualizowany.', $response->getData()->view);

        $this->assertDatabaseHas('posts', [
            'content' => 'Ten post został zaktualizowany.',
            'title' => 'Post zaktualizowany.',
        ]);

        $this->assertTrue(Auth::check());
    }

    public function testPostEditFullAsGuest()
    {
        $response = $this->get(route('admin.post.edit_full', [43]));

        $response->assertRedirect(route('login'));
    }

    public function testPostEditFullWithoutPermission()
    {
        $user = User::factory()->create();

        $post = Post::factory()->active()->publish()->withUser()->create();

        Auth::login($user);

        $response = $this->get(route('admin.post.edit_full', [$post->id]));

        $response->assertStatus(403);
    }

    public function testNoexistPostEditFull()
    {
        $user = User::factory()->admin()->create();

        Auth::login($user);

        $response = $this->get(route('admin.post.edit_full', [2327382]));

        $response->assertStatus(404);
    }

    public function testPostEditFull()
    {
        $user = User::factory()->admin()->create();

        $post = Post::factory()->active()->publish()->withUser()->create();

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
        $user = User::factory()->create();

        $post = Post::factory()->active()->publish()->withUser()->create();

        Auth::login($user);

        $response = $this->put(route('admin.post.update_full', [$post->id]));

        $response->assertStatus(403);
    }

    public function testNoexistPostUpdateFull()
    {
        $user = User::factory()->admin()->create();

        Auth::login($user);

        $response = $this->put(route('admin.post.update_full', [2327382]));

        $response->assertStatus(404);
    }

    public function testPostUpdateFullValidationFail()
    {
        $user = User::factory()->admin()->create();

        $post = Post::factory()->active()->publish()->withUser()->create();

        Auth::login($user);

        $response = $this->put(route('admin.post.update_full', [$post->id]), [
            'title' => 'Hdjshdjshdjshdjsds',
            'content_html' => 'Ten post został zaktualizowany.'
        ]);

        $response->assertSessionHasErrors(['categories', 'status', 'time_published_at', 'date_published_at']);

        $this->assertTrue(Auth::check());
    }

    public function testPostUpdateFull()
    {
        $user = User::factory()->admin()->create();

        $post = Post::factory()->active()->publish()->withUser()->create();

        $category = Category::factory()->active()->create();

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

        $this->assertTrue(Auth::check());
    }

    public function testPostUpdateStatusAsGuest()
    {
        $response = $this->patch(route('admin.post.update_status', [43]));

        $response->assertRedirect(route('login'));
    }

    public function testPostUpdateStatusWithoutPermission()
    {
        $user = User::factory()->create();

        $post = Post::factory()->active()->publish()->withUser()->create();

        Auth::login($user);

        $response = $this->patch(route('admin.post.update_status', [$post->id]));

        $response->assertStatus(403);
    }

    public function testNoexistPostUpdateStatus()
    {
        $user = User::factory()->admin()->create();

        Auth::login($user);

        $response = $this->patch(route('admin.post.update_status', [2327382]));

        $response->assertStatus(404);
    }

    public function testPostUpdateStatusValidationFail()
    {
        $user = User::factory()->admin()->create();

        $post = Post::factory()->active()->publish()->withUser()->create();

        Auth::login($user);

        $response = $this->patch(route('admin.post.update_status', [$post->id]), [
            'status' => 323,
        ]);

        $response->assertSessionHasErrors(['status']);

        $this->assertTrue(Auth::check());
    }

    public function testPostUpdateStatus()
    {
        $user = User::factory()->admin()->create();

        $post = Post::factory()->active()->publish()->withUser()->create();

        Auth::login($user);

        $response = $this->patch(route('admin.post.update_status', [$post->id]), [
            'status' => 0,
        ]);

        $response->assertOk()->assertJsonStructure(['success', 'view']);

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'status' => 0,
        ]);

        $this->assertTrue(Auth::check());
    }

    public function testPostDestroyAsGuest()
    {
        $response = $this->delete(route('admin.post.destroy', [43]));

        $response->assertRedirect(route('login'));
    }

    public function testPostDestroyWithoutPermission()
    {
        $user = User::factory()->create();

        $post = Post::factory()->active()->publish()->withUser()->create();

        Auth::login($user);

        $response = $this->delete(route('admin.post.destroy', [$post->id]));

        $response->assertStatus(403);
    }

    public function testNoexistPostDestroy()
    {
        $user = User::factory()->admin()->create();

        Auth::login($user);

        $response = $this->delete(route('admin.post.destroy', [2327382]));

        $response->assertStatus(404);
    }

    public function testPostDestroy()
    {
        $user = User::factory()->admin()->create();

        $post = Post::factory()->active()->publish()->withUser()->create();

        Auth::login($user);

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
        ]);

        $response = $this->delete(route('admin.post.destroy', [$post->id]), []);

        $response->assertOk()->assertJsonStructure(['success']);

        $this->assertDatabaseMissing('posts', [
            'id' => $post->id,
        ]);

        $this->assertTrue(Auth::check());
    }

    public function testPostDestroyGlobalAsGuest()
    {
        $response = $this->delete(route('admin.post.destroy_global'), []);

        $response->assertRedirect(route('login'));
    }

    public function testPostDestroyGlobalWithoutPermission()
    {
        $user = User::factory()->create();

        Auth::login($user);

        $response = $this->delete(route('admin.post.destroy_global'), []);

        $response->assertStatus(403);
    }

    public function testPostDestroyGlobalValidationFail()
    {
        $user = User::factory()->admin()->create();

        Auth::login($user);

        $response = $this->delete(route('admin.post.destroy_global', [
            'dasdas',
            'dada'
        ]));

        $response->assertSessionHasErrors(['select']);
    }

    public function testPostDestroyGlobal()
    {
        $user = User::factory()->admin()->create();

        $post = Post::factory()->count(10)->active()->publish()->withUser()->create();

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

        $this->assertTrue(Auth::check());
    }

    public function testPostCreateAsGuest()
    {
        $response = $this->get(route('admin.post.create'));

        $response->assertRedirect(route('login'));
    }

    public function testPostCreateWithoutPermission()
    {
        $user = User::factory()->create();

        Auth::login($user);

        $response = $this->get(route('admin.post.create'));

        $response->assertStatus(403);
    }

    public function testPostCreate()
    {
        $user = User::factory()->admin()->create();

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
        $user = User::factory()->create();

        Auth::login($user);

        $response = $this->post(route('admin.post.store'));

        $response->assertStatus(403);
    }

    public function testPostStoreValidationFail()
    {
        $user = User::factory()->admin()->create();

        Auth::login($user);

        $response = $this->post(route('admin.post.store'), [
            'title' => 'Hdjshdjshdjshdjsds',
            'content_html' => 'Ten post został zaktualizowany.'
        ]);

        $response->assertSessionHasErrors(['categories', 'status', 'date_published_at', 'time_published_at']);

        $this->assertTrue(Auth::check());
    }

    public function testPostStore()
    {
        $user = User::factory()->admin()->create();

        $category = Category::factory()->active()->create();

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

        $this->assertTrue(Auth::check());
    }
}
