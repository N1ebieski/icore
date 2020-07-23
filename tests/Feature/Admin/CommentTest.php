<?php

namespace N1ebieski\ICore\Tests\Feature\Admin;

use Tests\TestCase;
use N1ebieski\ICore\Models\User;
use N1ebieski\ICore\Models\Post;
use N1ebieski\ICore\Models\Comment\Post\Comment;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Auth;

class CommentTest extends TestCase
{
    use DatabaseTransactions;

    public function test_comment_post_index_as_guest()
    {
        $response = $this->get(route('admin.comment.post.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_comment_post_index_without_permission()
    {
        $user = factory(User::class)->create();

        Auth::login($user, true);

        $response = $this->get(route('admin.comment.post.index'));

        $response->assertStatus(403);
    }

    public function test_comment_post_index_paginate()
    {
        $user = factory(User::class)->states('admin')->create();

        $post = factory(Post::class)->states(['active', 'commentable', 'publish', 'with_user'])->create();

        $comment = factory(Comment::class, 50)->states(['active', 'with_user'])
            ->make()
            ->each(function($comment) use ($post) {
                $comment->morph()->associate($post)->save();
            });

        Auth::login($user, true);

        $response = $this->get(route('admin.comment.post.index', [
            'page' => 2,
            'orderby' => 'created_at|desc'
        ]));

        $response->assertViewIs('icore::admin.comment.index');
        $response->assertSee('class="pagination"');
        $response->assertSeeInOrder([$comment[30]->title, $comment[30]->shortContent]);
    }

    public function test_comment_show_as_guest()
    {
        $response = $this->get(route('admin.comment.show', [32]));

        $response->assertRedirect(route('login'));
    }

    public function test_comment_show_without_permission()
    {
        $user = factory(User::class)->create();

        $post = factory(Post::class)->states(['active', 'commentable', 'publish', 'with_user'])
            ->create();

        $comment = factory(Comment::class)->states(['active', 'with_user'])->make();
        $comment->morph()->associate($post)->save();

        Auth::login($user, true);

        $response = $this->get(route('admin.comment.show', [$comment->id]));

        $response->assertStatus(403);
    }

    public function test_noexist_comment_show()
    {
        $user = factory(User::class)->states('admin')->create();

        Auth::login($user, true);

        $response = $this->get(route('admin.comment.show', [4343434]));

        $response->assertStatus(404);
    }

    public function test_comment_show()
    {
        $user = factory(User::class)->states('admin')->create();

        $post = factory(Post::class)->states(['active', 'commentable', 'publish', 'with_user'])
            ->create();

        $parent = factory(Comment::class)->states(['active', 'with_user'])->make();
        $parent->morph()->associate($post)->save();

        $comment = factory(Comment::class)->states(['active', 'with_user'])
            ->make([
                'parent_id' => $parent->id
            ]);

        $comment->morph()->associate($post)->save();

        Auth::login($user, true);

        $response = $this->get(route('admin.comment.show', [$comment->id]));

        $response->assertOk()->assertJsonStructure(['success', 'view']);
        $this->assertContains($parent->content, $response->getData()->view);
        $this->assertContains($comment->content, $response->getData()->view);

    }

    public function test_comment_post_create_as_guest()
    {
        $response = $this->get(route('admin.comment.post.create', [99]));

        $response->assertRedirect(route('login'));
    }

    public function test_comment_noexist_post_create()
    {
        $user = factory(User::class)->create();

        Auth::login($user, true);

        $response = $this->get(route('admin.comment.post.create', [9999]));

        $response->assertStatus(404);

        $this->assertTrue(Auth::check());
    }

    public function test_comment_post_create_without_permission()
    {
        $user = factory(User::class)->create();

        $post = factory(Post::class)->states(['active', 'commentable', 'publish', 'with_user'])
            ->create();

        Auth::login($user, true);

        $response = $this->get(route('admin.comment.post.create', [$post->id]));

        $response->assertStatus(403);

        $this->assertTrue(Auth::check());
    }

    public function test_children_comment_post_create()
    {
        $user = factory(User::class)->states('admin')->create();

        $post = factory(Post::class)->states(['active', 'commentable', 'publish', 'with_user'])
            ->create();

        $parent = factory(Comment::class)->states(['active', 'with_user'])->make();
        $parent->morph()->associate($post)->save();

        Auth::login($user, true);

        $response = $this->get(route('admin.comment.post.create', [$post->id, 'parent_id' => $parent->id]));

        $response->assertOk()->assertJsonStructure(['success', 'view']);
        $this->assertContains(route('admin.comment.post.store', [$post->id]), $response->getData()->view);

        $this->assertTrue(Auth::check());
    }

    public function test_comment_post_store_as_guest()
    {
        $response = $this->post(route('admin.comment.post.store', [99]), []);

        $response->assertRedirect(route('login'));
    }

    public function test_comment_noexist_post_store()
    {
        $user = factory(User::class)->create();

        Auth::login($user, true);

        $response = $this->post(route('admin.comment.post.store', [9999]), []);

        $response->assertStatus(404);

        $this->assertTrue(Auth::check());
    }

    public function test_comment_post_store_without_permission()
    {
        $user = factory(User::class)->create();

        $post = factory(Post::class)->states(['active', 'commentable', 'publish', 'with_user'])
            ->create();

        Auth::login($user, true);

        $response = $this->post(route('admin.comment.post.store', [$post->id]), []);

        $response->assertStatus(403);

        $this->assertTrue(Auth::check());
    }

    public function test_children_noexist_comment_post_store()
    {
        $user = factory(User::class)->states('admin')->create();

        $post = factory(Post::class)->states(['active', 'commentable', 'publish', 'with_user'])
            ->create();

        Auth::login($user, true);

        $response = $this->post(route('admin.comment.post.store', [$post->id]), [
            'content' => '<b>Komentarz</b> zostal dodany. <script>dsdasd</script>',
            'parent_id' => 3894839
        ]);

        $response->assertSessionHasErrors(['parent_id']);

        $this->assertTrue(Auth::check());
    }

    public function test_children_comment_post_store()
    {
        $user = factory(User::class)->states('admin')->create();

        $post = factory(Post::class)->states(['active', 'commentable', 'publish', 'with_user'])
            ->create();

        $parent = factory(Comment::class)->states(['active', 'with_user'])->make();
        $parent->morph()->associate($post)->save();

        Auth::login($user, true);

        $response = $this->post(route('admin.comment.post.store', [$post->id]), [
            'content' => '<b>Komentarz</b> zostal dodany. <script>dsdasd</script>',
            'parent_id' => $parent->id
        ]);

        $response->assertOk()->assertJsonStructure(['success', 'view']);
        $this->assertContains('Komentarz zostal dodany. dsdasd', $response->getData()->view);

        $this->assertDatabaseHas('comments', [
            'content' => 'Komentarz zostal dodany. dsdasd',
            'parent_id' => $parent->id,
            'model_id' => $post->id,
            'model_type' => 'N1ebieski\\ICore\\Models\\Post'
        ]);

        $this->assertTrue(Auth::check());
    }

    public function test_comment_edit_as_guest()
    {
        $response = $this->get(route('admin.comment.edit', [99]));

        $response->assertRedirect(route('login'));
    }

    public function test_noexist_comment_edit()
    {
        $user = factory(User::class)->create();

        Auth::login($user, true);

        $response = $this->get(route('admin.comment.edit', [9999]));

        $response->assertStatus(404);

        $this->assertTrue(Auth::check());
    }

    public function test_comment_edit_without_permission()
    {
        $user = factory(User::class)->create();

        $post = factory(Post::class)->states(['active', 'commentable', 'publish', 'with_user'])
            ->create();

        $comment = factory(Comment::class)->states(['active', 'with_user'])->make();
        $comment->morph()->associate($post)->save();

        Auth::login($user, true);

        $response = $this->get(route('admin.comment.edit', [$comment->id]));

        $response->assertStatus(403);

        $this->assertTrue(Auth::check());
    }

    public function test_comment_edit()
    {
        $user = factory(User::class)->states('admin')->create();

        $post = factory(Post::class)->states(['active', 'commentable', 'publish', 'with_user'])
            ->create();

        $comment = factory(Comment::class)->states(['active', 'with_user'])->make();
        $comment->morph()->associate($post)->save();

        Auth::login($user, true);

        $response = $this->get(route('admin.comment.edit', [$comment->id]));

        $response->assertOk()->assertJsonStructure(['success', 'view']);
        $this->assertContains(route('admin.comment.update', [$comment->id]), $response->getData()->view);
        $this->assertContains($comment->content, $response->getData()->view);

        $this->assertTrue(Auth::check());
    }

    public function test_comment_update_as_guest()
    {
        $response = $this->put(route('admin.comment.update', [99]), []);

        $response->assertRedirect(route('login'));
    }

    public function test_noexist_comment_update()
    {
        $user = factory(User::class)->create();

        Auth::login($user, true);

        $response = $this->put(route('admin.comment.update', [9999]), []);

        $response->assertStatus(404);

        $this->assertTrue(Auth::check());
    }

    public function test_comment_update_without_permission()
    {
        $user = factory(User::class)->create();

        $post = factory(Post::class)->states(['active', 'commentable', 'publish', 'with_user'])
            ->create();

        $comment = factory(Comment::class)->states(['active', 'with_user'])->make();
        $comment->morph()->associate($post)->save();

        Auth::login($user, true);

        $response = $this->put(route('admin.comment.update', [$comment->id]), []);

        $response->assertStatus(403);

        $this->assertTrue(Auth::check());
    }

    public function test_comment_update_validation_fail()
    {
        $user = factory(User::class)->states('admin')->create();

        $post = factory(Post::class)->states(['active', 'commentable', 'publish', 'with_user'])
            ->create();

        $comment = factory(Comment::class)->states(['active', 'with_user'])->make();
        $comment->morph()->associate($post)->save();

        Auth::login($user, true);

        $response = $this->put(route('admin.comment.update', [$comment->id]), [
            'content' => ''
        ]);

        $response->assertSessionHasErrors(['content']);

        $this->assertTrue(Auth::check());
    }

    public function test_comment_update()
    {
        $user = factory(User::class)->states('admin')->create();

        $post = factory(Post::class)->states(['active', 'commentable', 'publish', 'with_user'])
            ->create();

        $comment = factory(Comment::class)->states(['active', 'with_user'])->make();
        $comment->morph()->associate($post)->save();

        Auth::login($user, true);

        $response = $this->put(route('admin.comment.update', [$comment->id]), [
            'content' => '<b>Komentarz</b> został zaktualizowany. <script>dsadad</script>'
        ]);

        $response->assertOk()->assertJsonStructure(['success', 'view']);
        $this->assertContains('Komentarz został zaktualizowany. dsadad', $response->getData()->view);

        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
            'content' => 'Komentarz został zaktualizowany. dsadad'
        ]);

        $this->assertTrue(Auth::check());
    }

    public function test_comment_updateStatus_as_guest()
    {
        $response = $this->patch(route('admin.comment.update_status', [43]));

        $response->assertRedirect(route('login'));
    }

    public function test_comment_updateStatus_without_permission()
    {
        $user = factory(User::class)->create();

        $post = factory(Post::class)->states(['active', 'commentable', 'publish', 'with_user'])
            ->create();

        $comment = factory(Comment::class)->states(['active', 'with_user'])->make();
        $comment->morph()->associate($post)->save();

        Auth::login($user, true);

        $response = $this->patch(route('admin.comment.update_status', [$comment->id]));

        $response->assertStatus(403);
    }

    public function test_noexist_comment_updateStatus()
    {
        $user = factory(User::class)->states('admin')->create();

        Auth::login($user, true);

        $response = $this->patch(route('admin.comment.update_status', [2327382]));

        $response->assertStatus(404);
    }

    public function test_comment_updateStatus_validation_fail()
    {
        $user = factory(User::class)->states('admin')->create();

        $post = factory(Post::class)->states(['active', 'commentable', 'publish', 'with_user'])
            ->create();

        $comment = factory(Comment::class)->states(['active', 'with_user'])->make();
        $comment->morph()->associate($post)->save();

        Auth::login($user, true);

        $response = $this->patch(route('admin.comment.update_status', [$comment->id]), [
            'status' => 323,
        ]);

        $response->assertSessionHasErrors(['status']);

        $this->assertTrue(Auth::check());
    }

    public function test_comment_updateStatus()
    {
        $user = factory(User::class)->states('admin')->create();

        $post = factory(Post::class)->states(['active', 'commentable', 'publish', 'with_user'])
            ->create();

        $comment = factory(Comment::class)->states(['active', 'with_user'])->make();
        $comment->morph()->associate($post)->save();

        Auth::login($user, true);

        $response = $this->patch(route('admin.comment.update_status', [$comment->id]), [
            'status' => 0,
        ]);

        $response->assertOk()->assertJsonStructure(['success', 'ancestors', 'descendants']);

        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
            'status' => 0,
        ]);

        $this->assertTrue(Auth::check());
    }

    public function test_comment_updateCensored_as_guest()
    {
        $response = $this->patch(route('admin.comment.update_censored', [43]));

        $response->assertRedirect(route('login'));
    }

    public function test_comment_updateCensored_without_permission()
    {
        $user = factory(User::class)->create();

        $post = factory(Post::class)->states(['active', 'commentable', 'publish', 'with_user'])
            ->create();

        $comment = factory(Comment::class)->states(['active', 'with_user'])->make();
        $comment->morph()->associate($post)->save();

        Auth::login($user, true);

        $response = $this->patch(route('admin.comment.update_censored', [$comment->id]));

        $response->assertStatus(403);
    }

    public function test_noexist_comment_updateCensored()
    {
        $user = factory(User::class)->states('admin')->create();

        Auth::login($user, true);

        $response = $this->patch(route('admin.comment.update_censored', [43423424]));

        $response->assertStatus(404);
    }

    public function test_comment_updateCensored_validation_fail()
    {
        $user = factory(User::class)->states('admin')->create();

        $post = factory(Post::class)->states(['active', 'commentable', 'publish', 'with_user'])
            ->create();

        $comment = factory(Comment::class)->states(['active', 'with_user'])->make();
        $comment->morph()->associate($post)->save();

        Auth::login($user, true);

        $response = $this->patch(route('admin.comment.update_censored', [$comment->id]), [
            'censored' => 323,
        ]);

        $response->assertSessionHasErrors(['censored']);

        $this->assertTrue(Auth::check());
    }

    public function test_comment_updateCensored()
    {
        $user = factory(User::class)->states('admin')->create();

        $post = factory(Post::class)->states(['active', 'commentable', 'publish', 'with_user'])
            ->create();

        $comment = factory(Comment::class)->states(['active', 'with_user'])->make();
        $comment->morph()->associate($post)->save();

        Auth::login($user, true);

        $response = $this->patch(route('admin.comment.update_censored', [$comment->id]), [
            'censored' => 1,
        ]);

        $response->assertOk()->assertJsonStructure(['success', 'view']);

        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
            'censored' => 1,
        ]);

        $this->assertTrue(Auth::check());
    }

    public function test_comment_destroy_as_guest()
    {
        $response = $this->delete(route('admin.comment.destroy', [43]));

        $response->assertRedirect(route('login'));
    }

    public function test_comment_destroy_without_permission()
    {
        $user = factory(User::class)->create();

        $post = factory(Post::class)->states(['active', 'commentable', 'publish', 'with_user'])
            ->create();

        $comment = factory(Comment::class)->states(['active', 'with_user'])->make();
        $comment->morph()->associate($post)->save();

        Auth::login($user, true);

        $response = $this->delete(route('admin.comment.destroy', [$comment->id]));

        $response->assertStatus(403);
    }

    public function test_noexist_comment_destroy()
    {
        $user = factory(User::class)->states('admin')->create();

        Auth::login($user, true);

        $response = $this->delete(route('admin.comment.destroy', [2327382]));

        $response->assertStatus(404);
    }

    public function test_comment_destroy()
    {
        $user = factory(User::class)->states('admin')->create();

        $post = factory(Post::class)->states(['active', 'commentable', 'publish', 'with_user'])
            ->create();

        $comment = factory(Comment::class)->states(['active', 'with_user'])->make();
        $comment->morph()->associate($post)->save();

        Auth::login($user, true);

        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
        ]);

        $response = $this->delete(route('admin.comment.destroy', [$comment->id]), []);

        $response->assertOk()->assertJsonStructure(['success']);

        $this->assertDatabaseMissing('comments', [
            'id' => $comment->id,
        ]);

        $this->assertTrue(Auth::check());
    }

    public function test_comment_destroyGlobal_as_guest()
    {
        $response = $this->delete(route('admin.comment.destroy_global'), []);

        $response->assertRedirect(route('login'));
    }

    public function test_comment_destroyGlobal_without_permission()
    {
        $user = factory(User::class)->create();

        Auth::login($user, true);

        $response = $this->delete(route('admin.comment.destroy_global'), []);

        $response->assertStatus(403);
    }

    public function test_comment_destroyGlobal_validation_fail()
    {
        $user = factory(User::class)->states('admin')->create();

        Auth::login($user, true);

        $response = $this->delete(route('admin.comment.destroy_global', [
            'dasdas',
            'dada'
        ]));

        $response->assertSessionHasErrors(['select']);
    }

    public function test_comment_destroyGlobal()
    {
        $user = factory(User::class)->states('admin')->create();

        $post = factory(Post::class)->states(['active', 'commentable', 'publish', 'with_user'])
            ->create();

        $comment = factory(Comment::class, 10)->states(['active', 'with_user'])
            ->make()
            ->each(function($comment) use ($post) {
                $comment->morph()->associate($post)->save();
            });

        Auth::login($user, true);

        $this->get(route('admin.comment.post.index'));

        $select = collect($comment)->pluck('id')->take(5)->toArray();

        $response = $this->delete(route('admin.comment.destroy_global'), [
            'select' => $select,
        ]);

        $response->assertRedirect(route('admin.comment.post.index'));
        $response->assertSessionHas('success');

        $deleted = Comment::whereIn('id', $select)->count();

        $this->assertTrue($deleted === 0);

        $this->assertTrue(Auth::check());
    }

}
