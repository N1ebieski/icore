<?php

namespace N1ebieski\ICore\Tests\Feature\Web;

use Tests\TestCase;
use N1ebieski\ICore\Models\User;
use N1ebieski\ICore\Models\Post;
use N1ebieski\ICore\Models\BanValue;
use N1ebieski\ICore\Models\Comment\Post\Comment;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Auth;

class CommentTest extends TestCase
{
    use DatabaseTransactions;

    public function test_noexist_comment_take()
    {
        $response = $this->post(route('web.comment.take', [9999]), []);

        $response->assertStatus(404);
    }

    public function test_comment_take()
    {
        $post = factory(Post::class)->states(['active', 'with_user', 'commentable'])->create();

        $parent = factory(Comment::class)->states(['active', 'with_user'])->make();
        $parent->morph()->associate($post)->save();

        $comment = factory(Comment::class, 15)->states(['active', 'with_user'])
            ->make()
            ->each(function($item) use ($parent, $post) {
                $item->morph()->associate($post);
                $item->parent_id = $parent->id;
                $item->save();
            });

        $response = $this->post(route('web.comment.take', [$parent->id]), [
            'except' => collect($comment)->pluck('id')->take(5)->toArray(),
            'orderby' => 'created_at|asc'
        ]);

        $response->assertOk()->assertJsonStructure(['success', 'view']);
        $this->assertContains(route('web.comment.take', [$parent->id]), $response->getData()->view);
        $this->assertContains($comment[9]->content, $response->getData()->view);
    }

    public function test_comment_post_create_as_guest()
    {
        $response = $this->get(route('web.comment.post.create', [99]));

        $response->assertRedirect(route('login'));
    }

    public function test_comment_noexist_post_create()
    {
        $user = factory(User::class)->states('user')->create();

        Auth::login($user, true);

        $response = $this->get(route('web.comment.post.create', [9999]));

        $response->assertStatus(404);

        $this->assertTrue(Auth::check());
    }

    public function test_comment_post_create_as_banned_user()
    {
        $user = factory(User::class)->states(['user', 'ban_user'])->create();

        Auth::login($user, true);

        $post = factory(Post::class)->states(['active', 'publish', 'with_user', 'commentable'])->create();

        $response = $this->get(route('web.comment.post.create', [$post->id]));

        $response->assertStatus(403);
        $response->assertSeeText('you are banned');

        $this->assertTrue(Auth::check());
    }

    public function test_comment_post_create_with_banned_ip()
    {
        $user = factory(User::class)->states('user')->create([
            'ip' => '127.0.0.1'
        ]);

        Auth::login($user, true);

        BanValue::create([
            'type' => 'ip',
            'value' => $user->ip
        ]);

        $post = factory(Post::class)->states(['active', 'publish', 'with_user', 'commentable'])->create();

        $response = $this->get(route('web.comment.post.create', [$post->id]));

        $response->assertStatus(403);
        $response->assertSeeText('you are banned');

        $this->assertTrue(Auth::check());
    }

    public function test_comment_post_comment_disable_create()
    {
        $user = factory(User::class)->states('user')->create();

        Auth::login($user, true);

        $post = factory(Post::class)->states(['active', 'publish', 'with_user', 'not_commentable'])->create();

        $response = $this->get(route('web.comment.post.create', [$post->id]));

        $response->assertStatus(403);
        $response->assertSeeText('disabled for this post');

        $this->assertTrue(Auth::check());
    }

    public function test_comment_post_create()
    {
        $user = factory(User::class)->states('user')->create();

        $post = factory(Post::class)->states(['active', 'publish', 'with_user', 'commentable'])->create();

        Auth::login($user, true);

        $response = $this->get(route('web.comment.post.create', [$post->id]));

        $response->assertOk()->assertJsonStructure(['success', 'view']);
        $this->assertContains(route('web.comment.post.store', [$post->id]), $response->getData()->view);

        $this->assertTrue(Auth::check());
    }

    public function test_comment_post_store_as_guest()
    {
        $response = $this->post(route('web.comment.post.store', [99]), []);

        $response->assertRedirect(route('login'));
    }

    public function test_comment_noexist_post_store()
    {
        $user = factory(User::class)->states('user')->create();

        Auth::login($user, true);

        $response = $this->post(route('web.comment.post.store', [9999]), []);

        $response->assertStatus(404);

        $this->assertTrue(Auth::check());
    }

    public function test_root_comment_post_store_with_banned_word()
    {
        $this->mock(\N1ebieski\ICore\Rules\Recaptcha_v2Rule::class, function ($mock) {
            $mock->shouldReceive('validate')->andReturn(true);
        });

        $user = factory(User::class)->states('user')->create();

        $post = factory(Post::class)->states(['active', 'publish', 'with_user', 'commentable'])
            ->create();

        Auth::login($user, true);

        BanValue::create([
            'type' => 'word',
            'value' => 'DUPA'
        ]);

        $response = $this->post(route('web.comment.post.store', [$post->id]), [
            'content' => 'Ddadsd DUPA sdadadas. <script>dsdasd</script>',
            'g-recaptcha-response' => 'dsadasd'
        ]);

        $response->assertSessionHasErrors('content');
        $this->assertContains('DUPA', session('errors')->get('content')[0]);

        $this->assertTrue(Auth::check());
    }

    public function test_comment_post_with_disable_comments_store()
    {
        $this->mock(\N1ebieski\ICore\Rules\Recaptcha_v2Rule::class, function ($mock) {
            $mock->shouldReceive('validate')->andReturn(true);
        });

        $user = factory(User::class)->states('user')->create();

        $post = factory(Post::class)->states(['active', 'publish', 'with_user', 'not_commentable'])
            ->create();

        Auth::login($user, true);

        $response = $this->post(route('web.comment.post.store', [$post->id]), [
            'content' => '<b>Komentarz</b> zostal dodany. <script>dsdasd</script>',
            'g-recaptcha-response' => 'dsadasd'
        ]);

        $response->assertStatus(403);
        $response->assertSeeText('disabled for this post');
    }

    public function test_root_comment_post_store()
    {
        $this->mock(\N1ebieski\ICore\Rules\Recaptcha_v2Rule::class, function ($mock) {
            $mock->shouldReceive('validate')->andReturn(true);
        });

        $user = factory(User::class)->states('user')->create();

        $post = factory(Post::class)->states(['active', 'publish', 'with_user', 'commentable'])
            ->create();

        Auth::login($user, true);

        $response = $this->post(route('web.comment.post.store', [$post->id]), [
            'content' => '<b>Komentarz</b> zostal dodany. <script>dsdasd</script>',
            'g-recaptcha-response' => 'dsadasd'
        ]);

        $response->assertOk()->assertJsonStructure(['success', 'view']);
        $this->assertContains('Komentarz zostal dodany. dsdasd', $response->getData()->view);

        $this->assertDatabaseHas('comments', [
            'content' => 'Komentarz zostal dodany. dsdasd',
            'parent_id' => null,
            'model_id' => $post->id,
            'model_type' => 'N1ebieski\\ICore\\Models\\Post'
        ]);

        $this->assertTrue(Auth::check());
    }

    public function test_children_noexist_comment_post_store()
    {
        $user = factory(User::class)->states('user')->create();

        $post = factory(Post::class)->states(['active', 'publish', 'with_user', 'commentable'])
            ->create();

        Auth::login($user, true);

        $response = $this->post(route('web.comment.post.store', [$post->id]), [
            'content' => '<b>Komentarz</b> zostal dodany. <script>dsdasd</script>',
            'parent_id' => 3894839
        ]);

        $response->assertSessionHasErrors(['parent_id']);

        $this->assertTrue(Auth::check());
    }

    public function test_children_comment_post_store()
    {
        $this->mock(\N1ebieski\ICore\Rules\Recaptcha_v2Rule::class, function ($mock) {
            $mock->shouldReceive('validate')->andReturn(true);
        });

        $user = factory(User::class)->states('user')->create();

        $post = factory(Post::class)->states(['active', 'publish', 'with_user', 'commentable'])
            ->create();

        $parent = factory(Comment::class)->states(['active', 'with_user'])->make();
        $parent->morph()->associate($post)->save();

        Auth::login($user, true);

        $response = $this->post(route('web.comment.post.store', [$post->id]), [
            'content' => '<b>Komentarz</b> zostal dodany. <script>dsdasd</script>',
            'parent_id' => $parent->id,
            'g-recaptcha-response' => 'dsadasd'
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
        $response = $this->get(route('web.comment.edit', [99]));

        $response->assertRedirect(route('login'));
    }

    public function test_noexist_comment_edit()
    {
        $user = factory(User::class)->states('user')->create();

        Auth::login($user, true);

        $response = $this->get(route('web.comment.edit', [9999]));

        $response->assertStatus(404);

        $this->assertTrue(Auth::check());
    }

    public function test_foreign_comment_edit()
    {
        $post = factory(Post::class)->states(['active', 'publish', 'with_user', 'commentable'])
            ->create();

        $comment = factory(Comment::class)->states(['active', 'with_user'])->make();
        $comment->morph()->associate($post)->save();

        $user = factory(User::class)->states('user')->create();

        Auth::login($user, true);

        $response = $this->get(route('web.comment.edit', [$comment->id]));

        $response->assertStatus(403);

        $this->assertTrue(Auth::check());
    }

    public function test_comment_edit()
    {
        $user = factory(User::class)->states('user')->create();

        $post = factory(Post::class)->states(['active', 'publish', 'with_user', 'commentable'])
            ->create();

        $comment = factory(Comment::class)->states('active')->make();
        $comment->morph()->associate($post);
        $comment->user()->associate($user);
        $comment->save();

        Auth::login($user, true);

        $response = $this->get(route('web.comment.edit', [$comment->id]));

        $response->assertOk()->assertJsonStructure(['success', 'view']);
        $this->assertContains(route('web.comment.update', [$comment->id]), $response->getData()->view);
        $this->assertContains($comment->content, $response->getData()->view);

        $this->assertTrue(Auth::check());
    }

    public function test_comment_update_as_guest()
    {
        $response = $this->put(route('web.comment.update', [99]), []);

        $response->assertRedirect(route('login'));
    }

    public function test_noexist_comment_update()
    {
        $user = factory(User::class)->states('user')->create();

        Auth::login($user, true);

        $response = $this->put(route('web.comment.update', [9999]), []);

        $response->assertStatus(404);

        $this->assertTrue(Auth::check());
    }

    public function test_foreign_comment_update()
    {
        $post = factory(Post::class)->states(['active', 'publish', 'with_user', 'commentable'])
            ->create();

        $comment = factory(Comment::class)->states(['active', 'with_user'])->make();
        $comment->morph()->associate($post)->save();

        $user = factory(User::class)->states('user')->create();

        Auth::login($user, true);

        $response = $this->put(route('web.comment.update', [$comment->id]), [
            'content' => 'Hdsjdhsjdsj'
        ]);

        $response->assertStatus(403);

        $this->assertTrue(Auth::check());
    }

    public function test_comment_update_validation_fail()
    {
        $user = factory(User::class)->states('user')->create();

        $post = factory(Post::class)->states(['active', 'publish', 'with_user', 'commentable'])
            ->create();

        $comment = factory(Comment::class)->states('active')->make();
        $comment->morph()->associate($post);
        $comment->user()->associate($user);
        $comment->save();

        Auth::login($user, true);

        $response = $this->put(route('web.comment.update', [$comment->id]), [
            'content' => ''
        ]);

        $response->assertSessionHasErrors(['content']);

        $this->assertTrue(Auth::check());
    }

    public function test_comment_update()
    {
        $user = factory(User::class)->states('user')->create();

        $post = factory(Post::class)->states(['active', 'publish', 'with_user', 'commentable'])
            ->create();

        $comment = factory(Comment::class)->states('active')->make();
        $comment->morph()->associate($post);
        $comment->user()->associate($user);
        $comment->save();

        Auth::login($user, true);

        $response = $this->put(route('web.comment.update', [$comment->id]), [
            'content' => '<b>Komentarz</b> zostal edytowany. <script>dsadad</script>'
        ]);

        $response->assertOk()->assertJsonStructure(['success', 'view']);
        $this->assertContains('Komentarz zostal edytowany. dsadad', $response->getData()->view);

        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
            'content' => 'Komentarz zostal edytowany. dsadad'
        ]);

        $this->assertTrue(Auth::check());
    }
}
