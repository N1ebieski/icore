<?php

namespace N1ebieski\ICore\Tests\Feature\Admin;

use Carbon\Carbon;
use Tests\TestCase;
use N1ebieski\ICore\Models\User;
use N1ebieski\ICore\Models\Post;
use N1ebieski\ICore\Models\Category\Post\Category;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Auth;
use Faker\Factory as Faker;

class PostTest extends TestCase
{
    use DatabaseTransactions;

    public function test_post_index_as_guest()
    {
        $response = $this->get(route('admin.post.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_post_index_without_permission()
    {
        $user = factory(User::class)->create();

        Auth::login($user, true);

        $response = $this->get(route('admin.post.index'));

        $response->assertStatus(403);
    }

    public function test_post_index_paginate()
    {
        $user = factory(User::class)->states('admin')->create();

        $post = factory(Post::class, 50)->states(['active', 'publish', 'with_user'])->create();

        Auth::login($user, true);

        $response = $this->get(route('admin.post.index', [
            'page' => 2,
            'orderby' => 'created_at|asc'
        ]));

        $response->assertViewIs('icore::admin.post.index');
        $response->assertSee('class="pagination"');
        $response->assertSeeInOrder([$post[30]->title, $post[30]->shortContent]);
    }

    public function test_post_edit_as_guest()
    {
        $response = $this->get(route('admin.post.edit', [43]));

        $response->assertRedirect(route('login'));
    }

    public function test_post_edit_without_permission()
    {
        $user = factory(User::class)->create();

        $post = factory(Post::class)->states(['active', 'publish', 'with_user'])->create();

        Auth::login($user, true);

        $response = $this->get(route('admin.post.edit', [$post->id]));

        $response->assertStatus(403);
    }

    public function test_noexist_post_edit()
    {
        $user = factory(User::class)->states('admin')->create();

        Auth::login($user, true);

        $response = $this->get(route('admin.post.edit', [2327382]));

        $response->assertStatus(404);
    }

    public function test_post_edit()
    {
        $user = factory(User::class)->states('admin')->create();

        $post = factory(Post::class)->states(['active', 'publish', 'with_user'])->create();

        Auth::login($user, true);

        $response = $this->get(route('admin.post.edit', [$post->id]));

        $response->assertOk()->assertJsonStructure(['success', 'view']);
        $this->assertStringContainsString($post->content, $response->getData()->view);
        $this->assertStringContainsString(route('admin.post.update', [$post->id]), $response->getData()->view);

    }

    public function test_post_update_as_guest()
    {
        $response = $this->put(route('admin.post.update', [43]));

        $response->assertRedirect(route('login'));
    }

    public function test_post_update_without_permission()
    {
        $user = factory(User::class)->create();

        $post = factory(Post::class)->states(['active', 'publish', 'with_user'])->create();

        Auth::login($user, true);

        $response = $this->put(route('admin.post.update', [$post->id]));

        $response->assertStatus(403);
    }

    public function test_noexist_post_update()
    {
        $user = factory(User::class)->states('admin')->create();

        Auth::login($user, true);

        $response = $this->put(route('admin.post.update', [2327382]));

        $response->assertStatus(404);
    }

    public function test_post_update_validation_fail()
    {
        $user = factory(User::class)->states('admin')->create();

        $post = factory(Post::class)->states(['active', 'publish', 'with_user'])->create();

        Auth::login($user, true);

        $response = $this->put(route('admin.post.update', [$post->id]), [
            'title' => '',
            'content_html' => 'Ten post został zaktualizowany.'
        ]);

        $response->assertSessionHasErrors(['title']);

        $this->assertTrue(Auth::check());
    }

    public function test_post_update()
    {
        $user = factory(User::class)->states('admin')->create();

        $post = factory(Post::class)->states(['active', 'publish', 'with_user'])->create();

        Auth::login($user, true);

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

    public function test_post_editFull_as_guest()
    {
        $response = $this->get(route('admin.post.edit_full', [43]));

        $response->assertRedirect(route('login'));
    }

    public function test_post_editFull_without_permission()
    {
        $user = factory(User::class)->create();

        $post = factory(Post::class)->states(['active', 'publish', 'with_user'])->create();

        Auth::login($user, true);

        $response = $this->get(route('admin.post.edit_full', [$post->id]));

        $response->assertStatus(403);
    }

    public function test_noexist_post_editFull()
    {
        $user = factory(User::class)->states('admin')->create();

        Auth::login($user, true);

        $response = $this->get(route('admin.post.edit_full', [2327382]));

        $response->assertStatus(404);
    }

    public function test_post_editFull()
    {
        $user = factory(User::class)->states('admin')->create();

        $post = factory(Post::class)->states(['active', 'publish', 'with_user'])->create();

        Auth::login($user, true);

        $response = $this->get(route('admin.post.edit_full', [$post->id]));

        $response->assertOk()->assertViewIs('icore::admin.post.edit_full');
        $response->assertSeeInOrder([$post->title, $post->content]);
        $response->assertSee(route('admin.post.update_full', [$post->id]));

    }

    public function test_post_updateFull_as_guest()
    {
        $response = $this->put(route('admin.post.update_full', [43]));

        $response->assertRedirect(route('login'));
    }

    public function test_post_updateFull_without_permission()
    {
        $user = factory(User::class)->create();

        $post = factory(Post::class)->states(['active', 'publish', 'with_user'])->create();

        Auth::login($user, true);

        $response = $this->put(route('admin.post.update_full', [$post->id]));

        $response->assertStatus(403);
    }

    public function test_noexist_post_updateFull()
    {
        $user = factory(User::class)->states('admin')->create();

        Auth::login($user, true);

        $response = $this->put(route('admin.post.update_full', [2327382]));

        $response->assertStatus(404);
    }

    public function test_post_updateFull_validation_fail()
    {
        $user = factory(User::class)->states('admin')->create();

        $post = factory(Post::class)->states(['active', 'publish', 'with_user'])->create();

        Auth::login($user, true);

        $response = $this->put(route('admin.post.update_full', [$post->id]), [
            'title' => 'Hdjshdjshdjshdjsds',
            'content_html' => 'Ten post został zaktualizowany.'
        ]);

        $response->assertSessionHasErrors(['categories', 'status', 'time_published_at', 'date_published_at']);

        $this->assertTrue(Auth::check());
    }

    public function test_post_updateFull()
    {
        $user = factory(User::class)->states('admin')->create();

        $post = factory(Post::class)->states(['active', 'publish', 'with_user'])->create();

        $category = factory(Category::class)->states('active')->create();

        Auth::login($user, true);

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

    public function test_post_updateStatus_as_guest()
    {
        $response = $this->patch(route('admin.post.update_status', [43]));

        $response->assertRedirect(route('login'));
    }

    public function test_post_updateStatus_without_permission()
    {
        $user = factory(User::class)->create();

        $post = factory(Post::class)->states(['active', 'publish', 'with_user'])->create();

        Auth::login($user, true);

        $response = $this->patch(route('admin.post.update_status', [$post->id]));

        $response->assertStatus(403);
    }

    public function test_noexist_post_updateStatus()
    {
        $user = factory(User::class)->states('admin')->create();

        Auth::login($user, true);

        $response = $this->patch(route('admin.post.update_status', [2327382]));

        $response->assertStatus(404);
    }

    public function test_post_updateStatus_validation_fail()
    {
        $user = factory(User::class)->states('admin')->create();

        $post = factory(Post::class)->states(['active', 'publish', 'with_user'])->create();

        Auth::login($user, true);

        $response = $this->patch(route('admin.post.update_status', [$post->id]), [
            'status' => 323,
        ]);

        $response->assertSessionHasErrors(['status']);

        $this->assertTrue(Auth::check());
    }

    public function test_post_updateStatus()
    {
        $user = factory(User::class)->states('admin')->create();

        $post = factory(Post::class)->states(['active', 'publish', 'with_user'])->create();

        Auth::login($user, true);

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

    public function test_post_destroy_as_guest()
    {
        $response = $this->delete(route('admin.post.destroy', [43]));

        $response->assertRedirect(route('login'));
    }

    public function test_post_destroy_without_permission()
    {
        $user = factory(User::class)->create();

        $post = factory(Post::class)->states(['active', 'publish', 'with_user'])->create();

        Auth::login($user, true);

        $response = $this->delete(route('admin.post.destroy', [$post->id]));

        $response->assertStatus(403);
    }

    public function test_noexist_post_destroy()
    {
        $user = factory(User::class)->states('admin')->create();

        Auth::login($user, true);

        $response = $this->delete(route('admin.post.destroy', [2327382]));

        $response->assertStatus(404);
    }

    public function test_post_destroy()
    {
        $user = factory(User::class)->states('admin')->create();

        $post = factory(Post::class)->states(['active', 'publish', 'with_user'])->create();

        Auth::login($user, true);

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

    public function test_post_destroyGlobal_as_guest()
    {
        $response = $this->delete(route('admin.post.destroy_global'), []);

        $response->assertRedirect(route('login'));
    }

    public function test_post_destroyGlobal_without_permission()
    {
        $user = factory(User::class)->create();

        Auth::login($user, true);

        $response = $this->delete(route('admin.post.destroy_global'), []);

        $response->assertStatus(403);
    }

    public function test_post_destroyGlobal_validation_fail()
    {
        $user = factory(User::class)->states('admin')->create();

        Auth::login($user, true);

        $response = $this->delete(route('admin.post.destroy_global', [
            'dasdas',
            'dada'
        ]));

        $response->assertSessionHasErrors(['select']);
    }

    public function test_post_destroyGlobal()
    {
        $user = factory(User::class)->states('admin')->create();

        $post = factory(Post::class, 10)->states(['active', 'publish', 'with_user'])->create();

        Auth::login($user, true);

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

    public function test_post_create_as_guest()
    {
        $response = $this->get(route('admin.post.create'));

        $response->assertRedirect(route('login'));
    }

    public function test_post_create_without_permission()
    {
        $user = factory(User::class)->create();

        Auth::login($user, true);

        $response = $this->get(route('admin.post.create'));

        $response->assertStatus(403);
    }

    public function test_post_create()
    {
        $user = factory(User::class)->states('admin')->create();

        Auth::login($user, true);

        $response = $this->get(route('admin.post.create'));

        $response->assertOk()->assertViewIs('icore::admin.post.create');
        $response->assertSee(route('admin.post.store'));

    }

    public function test_post_store_as_guest()
    {
        $response = $this->post(route('admin.post.store'));

        $response->assertRedirect(route('login'));
    }

    public function test_post_store_without_permission()
    {
        $user = factory(User::class)->create();

        Auth::login($user, true);

        $response = $this->post(route('admin.post.store'));

        $response->assertStatus(403);
    }

    public function test_post_store_validation_fail()
    {
        $user = factory(User::class)->states('admin')->create();

        Auth::login($user, true);

        $response = $this->post(route('admin.post.store'), [
            'title' => 'Hdjshdjshdjshdjsds',
            'content_html' => 'Ten post został zaktualizowany.'
        ]);

        $response->assertSessionHasErrors(['categories', 'status', 'date_published_at', 'time_published_at']);

        $this->assertTrue(Auth::check());
    }

    public function test_post_store()
    {
        $user = factory(User::class)->states('admin')->create();

        $category = factory(Category::class)->states('active')->create();

        Auth::login($user, true);

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
