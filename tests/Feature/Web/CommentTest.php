<?php

namespace N1ebieski\ICore\Tests\Feature\Web;

use Tests\TestCase;
use N1ebieski\ICore\Models\Post;
use N1ebieski\ICore\Models\User;
use Illuminate\Support\Facades\Auth;
use N1ebieski\ICore\Models\BanValue;
use N1ebieski\ICore\Models\Comment\Post\Comment;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CommentTest extends TestCase
{
    use DatabaseTransactions;

    public function testNoexistCommentTake()
    {
        $response = $this->post(route('web.comment.take', [9999]), []);

        $response->assertStatus(404);
    }

    public function testCommentTake()
    {
        $post = Post::makeFactory()->active()->commentable()->withUser()->create();

        $parent = Comment::makeFactory()->active()->withUser()->for($post, 'morph')->create();

        $comment = Comment::makeFactory()->count(15)->active()->withUser()->for($post, 'morph')->create([
            'parent_id' => $parent->id
        ]);

        $response = $this->post(route('web.comment.take', [$parent->id]), [
            'filter' => [
                'except' => collect($comment)->pluck('id')->take(5)->toArray(),
                'orderby' => 'created_at|asc'
            ]
        ]);

        $response->assertOk()->assertJsonStructure(['success', 'view']);

        $this->assertStringContainsString(route('web.comment.take', [$parent->id]), $response->getData()->view);
        $this->assertStringContainsString($comment[9]->content, $response->getData()->view);
    }

    public function testCommentPostCreateAsGuest()
    {
        $response = $this->get(route('web.comment.post.create', [99]));

        $response->assertRedirect(route('login'));
    }

    public function testCommentNoexistPostCreate()
    {
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        $response = $this->get(route('web.comment.post.create', [9999]));

        $response->assertStatus(404);
    }

    public function testCommentPostCreateAsBannedUser()
    {
        $user = User::makeFactory()->user()->banUser()->create();

        Auth::login($user);

        $post = Post::makeFactory()->active()->publish()->commentable()->withUser()->create();

        $response = $this->get(route('web.comment.post.create', [$post->id]));

        $response->assertStatus(403);
        $response->assertSeeText('you are banned', false);
    }

    public function testCommentPostCreateWithBannedIp()
    {
        $user = User::makeFactory()->user()->create([
            'ip' => '127.0.0.1'
        ]);

        BanValue::makeFactory()->ip()->create([
            'value' => $user->ip
        ]);

        Auth::login($user);

        $post = Post::makeFactory()->active()->publish()->commentable()->withUser()->create();

        $response = $this->get(route('web.comment.post.create', [$post->id]));

        $response->assertStatus(403);
        $response->assertSeeText('you are banned', false);
    }

    public function testCommentPostCommentDisableCreate()
    {
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        $post = Post::makeFactory()->active()->publish()->withUser()->notCommentable()->create();

        $response = $this->get(route('web.comment.post.create', [$post->id]));

        $response->assertStatus(403);
        $response->assertSeeText('disabled for this post', false);
    }

    public function testCommentPostCreate()
    {
        $user = User::makeFactory()->user()->create();

        $post = Post::makeFactory()->active()->publish()->commentable()->withUser()->create();

        Auth::login($user);

        $response = $this->get(route('web.comment.post.create', [$post->id]));

        $response->assertOk()->assertJsonStructure(['success', 'view']);
        $this->assertStringContainsString(route('web.comment.post.store', [$post->id]), $response->getData()->view);
    }

    public function testCommentPostStoreAsGuest()
    {
        $response = $this->post(route('web.comment.post.store', [99]), []);

        $response->assertRedirect(route('login'));
    }

    public function testCommentNoexistPostStore()
    {
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        $response = $this->post(route('web.comment.post.store', [9999]), []);

        $response->assertStatus(404);
    }

    public function testRootCommentPostStoreWithBannedWord()
    {
        $this->mock(\N1ebieski\ICore\Rules\RecaptchaV2Rule::class, function ($mock) {
            $mock->shouldReceive('validate')->andReturn(true);
        });

        $user = User::makeFactory()->user()->create();

        $post = Post::makeFactory()->active()->publish()->commentable()->withUser()->create();

        Auth::login($user);

        BanValue::create([
            'type' => 'word',
            'value' => 'DUPA'
        ]);

        $response = $this->post(route('web.comment.post.store', [$post->id]), [
            'content' => 'Ddadsd DUPA sdadadas. <script>dsdasd</script>',
            'g-recaptcha-response' => 'dsadasd'
        ]);

        $response->assertSessionHasErrors('content');
        $this->assertStringContainsString('DUPA', session('errors')->get('content')[0]);
    }

    public function testCommentPostWithDisableCommentsStore()
    {
        $this->mock(\N1ebieski\ICore\Rules\RecaptchaV2Rule::class, function ($mock) {
            $mock->shouldReceive('validate')->andReturn(true);
        });

        $user = User::makeFactory()->user()->create();

        $post = Post::makeFactory()->active()->publish()->withUser()->notCommentable()->create();

        Auth::login($user);

        $response = $this->post(route('web.comment.post.store', [$post->id]), [
            'content' => '<b>Komentarz</b> zostal dodany. <script>dsdasd</script>',
            'g-recaptcha-response' => 'dsadasd'
        ]);

        $response->assertStatus(403);
        $response->assertSeeText('disabled for this post', false);
    }

    public function testRootCommentPostStore()
    {
        $this->mock(\N1ebieski\ICore\Rules\RecaptchaV2Rule::class, function ($mock) {
            $mock->shouldReceive('validate')->andReturn(true);
        });

        $user = User::makeFactory()->user()->create();

        $post = Post::makeFactory()->active()->publish()->commentable()->withUser()->create();

        Auth::login($user);

        $response = $this->post(route('web.comment.post.store', [$post->id]), [
            'content' => '<b>Komentarz</b> zostal dodany. <script>dsdasd</script>',
            'g-recaptcha-response' => 'dsadasd'
        ]);

        $response->assertOk()->assertJsonStructure(['success', 'view']);
        $this->assertStringContainsString('Komentarz zostal dodany. dsdasd', $response->getData()->view);

        $this->assertDatabaseHas('comments', [
            'content' => 'Komentarz zostal dodany. dsdasd',
            'parent_id' => null,
            'model_id' => $post->id,
            'model_type' => 'N1ebieski\\ICore\\Models\\Post'
        ]);
    }

    public function testChildrenNoexistCommentPostStore()
    {
        $user = User::makeFactory()->user()->create();

        $post = Post::makeFactory()->active()->publish()->commentable()->withUser()->create();

        Auth::login($user);

        $response = $this->post(route('web.comment.post.store', [$post->id]), [
            'content' => '<b>Komentarz</b> zostal dodany. <script>dsdasd</script>',
            'parent_id' => 3894839
        ]);

        $response->assertSessionHasErrors(['parent_id']);
    }

    public function testChildrenCommentPostStore()
    {
        $this->mock(\N1ebieski\ICore\Rules\RecaptchaV2Rule::class, function ($mock) {
            $mock->shouldReceive('validate')->andReturn(true);
        });

        $user = User::makeFactory()->user()->create();

        $post = Post::makeFactory()->active()->publish()->commentable()->withUser()->create();

        $parent = Comment::makeFactory()->active()->withUser()->for($post, 'morph')->create();

        Auth::login($user);

        $response = $this->post(route('web.comment.post.store', [$post->id]), [
            'content' => '<b>Komentarz</b> zostal dodany. <script>dsdasd</script>',
            'parent_id' => $parent->id,
            'g-recaptcha-response' => 'dsadasd'
        ]);

        $response->assertOk()->assertJsonStructure(['success', 'view']);

        $this->assertStringContainsString('Komentarz zostal dodany. dsdasd', $response->getData()->view);

        $this->assertDatabaseHas('comments', [
            'content' => 'Komentarz zostal dodany. dsdasd',
            'parent_id' => $parent->id,
            'model_id' => $post->id,
            'model_type' => 'N1ebieski\\ICore\\Models\\Post'
        ]);
    }

    public function testCommentEditAsGuest()
    {
        $response = $this->get(route('web.comment.edit', [99]));

        $response->assertRedirect(route('login'));
    }

    public function testNoexistCommentEdit()
    {
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        $response = $this->get(route('web.comment.edit', [9999]));

        $response->assertStatus(404);
    }

    public function testForeignCommentEdit()
    {
        $post = Post::makeFactory()->active()->publish()->commentable()->withUser()->create();

        $comment = Comment::makeFactory()->active()->withUser()->for($post, 'morph')->create();

        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        $response = $this->get(route('web.comment.edit', [$comment->id]));

        $response->assertStatus(403);
    }

    public function testCommentEdit()
    {
        $user = User::makeFactory()->user()->create();

        $post = Post::makeFactory()->active()->publish()->commentable()->withUser()->create();

        $comment = Comment::makeFactory()->active()->for($post, 'morph')->for($user)->create();

        Auth::login($user);

        $response = $this->get(route('web.comment.edit', [$comment->id]));

        $response->assertOk()->assertJsonStructure(['success', 'view']);

        $this->assertStringContainsString(route('web.comment.update', [$comment->id]), $response->getData()->view);
        $this->assertStringContainsString($comment->content, $response->getData()->view);
    }

    public function testCommentUpdateAsGuest()
    {
        $response = $this->put(route('web.comment.update', [99]), []);

        $response->assertRedirect(route('login'));
    }

    public function testNoexistCommentUpdate()
    {
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        $response = $this->put(route('web.comment.update', [9999]), []);

        $response->assertStatus(404);
    }

    public function testForeignCommentUpdate()
    {
        $post = Post::makeFactory()->active()->publish()->commentable()->withUser()->create();

        $comment = Comment::makeFactory()->active()->withUser()->for($post, 'morph')->create();

        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        $response = $this->put(route('web.comment.update', [$comment->id]), [
            'content' => 'Hdsjdhsjdsj'
        ]);

        $response->assertStatus(403);
    }

    public function testCommentUpdateValidationFail()
    {
        $user = User::makeFactory()->user()->create();

        $post = Post::makeFactory()->active()->publish()->commentable()->withUser()->create();

        $comment = Comment::makeFactory()->active()->for($post, 'morph')->for($user)->create();

        Auth::login($user);

        $response = $this->put(route('web.comment.update', [$comment->id]), [
            'content' => ''
        ]);

        $response->assertSessionHasErrors(['content']);
    }

    public function testCommentUpdate()
    {
        $user = User::makeFactory()->user()->create();

        $post = Post::makeFactory()->active()->publish()->commentable()->withUser()->create();

        $comment = Comment::makeFactory()->active()->for($post, 'morph')->for($user)->create();

        Auth::login($user);

        $response = $this->put(route('web.comment.update', [$comment->id]), [
            'content' => '<b>Komentarz</b> został zaktualizowany. <script>dsadad</script>'
        ]);

        $response->assertOk()->assertJsonStructure(['success', 'view']);

        $this->assertStringContainsString('Komentarz został zaktualizowany. dsadad', $response->getData()->view);

        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
            'content' => 'Komentarz został zaktualizowany. dsadad'
        ]);
    }
}
