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

namespace N1ebieski\ICore\Tests\Feature\Web;

use Tests\TestCase;
use Mockery\MockInterface;
use N1ebieski\ICore\Models\Post;
use N1ebieski\ICore\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use N1ebieski\ICore\Models\BanValue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Response as HttpResponse;
use N1ebieski\ICore\ValueObjects\BanValue\Type;
use N1ebieski\ICore\Models\Comment\Post\Comment;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CommentTest extends TestCase
{
    use DatabaseTransactions;

    public function testNoexistCommentTake(): void
    {
        $response = $this->post(route('web.comment.take', [9999]), []);

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testCommentTake(): void
    {
        /** @var Post */
        $post = Post::makeFactory()->active()->commentable()->withUser()->create();

        /** @var Comment */
        $parent = Comment::makeFactory()->active()->withUser()->for($post, 'morph')->create();

        /** @var Collection<Comment>|array<Comment> */
        $comments = Comment::makeFactory()->count(15)->active()->withUser()->for($post, 'morph')->create([
            'parent_id' => $parent->id
        ]);

        $response = $this->post(route('web.comment.take', [$parent->id]), [
            'filter' => [
                'except' => collect($comments)->pluck('id')->take(5)->toArray(),
                'orderby' => 'created_at|asc'
            ]
        ]);

        $response->assertOk()->assertJsonStructure(['view']);

        /** @var JsonResponse */
        $baseResponse = $response->baseResponse;

        $this->assertStringContainsString(
            route('web.comment.take', [$parent->id]),
            $baseResponse->getData()->view
        );

        $this->assertStringContainsString(
            $comments[9]->content,
            $baseResponse->getData()->view
        );
    }

    public function testCommentPostCreateAsGuest(): void
    {
        $response = $this->get(route('web.comment.post.create', [99]));

        $response->assertRedirect(route('login'));
    }

    public function testCommentNoexistPostCreate(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        $response = $this->get(route('web.comment.post.create', [9999]));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testCommentPostCreateAsBannedUser(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->banUser()->create();

        Auth::login($user);

        /** @var Post */
        $post = Post::makeFactory()->active()->publish()->commentable()->withUser()->create();

        $response = $this->get(route('web.comment.post.create', [$post->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
        $response->assertSeeText('you are banned', false);
    }

    public function testCommentPostCreateWithBannedIp(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create([
            'ip' => '127.0.0.1'
        ]);

        BanValue::makeFactory()->ip()->create([
            'value' => $user->ip
        ]);

        Auth::login($user);

        /** @var Post */
        $post = Post::makeFactory()->active()->publish()->commentable()->withUser()->create();

        $response = $this->get(route('web.comment.post.create', [$post->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
        $response->assertSeeText('you are banned', false);
    }

    public function testCommentPostCommentDisableCreate(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        /** @var Post */
        $post = Post::makeFactory()->active()->publish()->withUser()->notCommentable()->create();

        $response = $this->get(route('web.comment.post.create', [$post->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
        $response->assertSeeText('disabled for this post', false);
    }

    public function testCommentPostCreate(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        /** @var Post */
        $post = Post::makeFactory()->active()->publish()->commentable()->withUser()->create();

        Auth::login($user);

        $response = $this->get(route('web.comment.post.create', [$post->id]));

        $response->assertOk()->assertJsonStructure(['view']);

        /** @var JsonResponse */
        $baseResponse = $response->baseResponse;

        $this->assertStringContainsString(
            route('web.comment.post.store', [$post->id]),
            $baseResponse->getData()->view
        );
    }

    public function testCommentPostStoreAsGuest(): void
    {
        $response = $this->post(route('web.comment.post.store', [99]), []);

        $response->assertRedirect(route('login'));
    }

    public function testCommentNoexistPostStore(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        $response = $this->post(route('web.comment.post.store', [9999]), []);

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testRootCommentPostStoreWithBannedWord(): void
    {
        $this->mock(\N1ebieski\ICore\Rules\RecaptchaV2Rule::class, function (MockInterface $mock) {
            $mock->shouldReceive('passes')->once()->andReturn(true);
        });

        /** @var User */
        $user = User::makeFactory()->user()->create();

        /** @var Post */
        $post = Post::makeFactory()->active()->publish()->commentable()->withUser()->create();

        Auth::login($user);

        BanValue::makeFactory()->create([
            'type' => Type::WORD,
            'value' => 'DUPA'
        ]);

        $response = $this->post(route('web.comment.post.store', [$post->id]), [
            'content' => 'Ddadsd DUPA sdadadas. <script>dsdasd</script>',
            'g-recaptcha-response' => 'dsadasd'
        ]);

        $response->assertSessionHasErrors('content');

        $this->assertStringContainsString('DUPA', session('errors')->get('content')[0]);
    }

    public function testCommentPostWithDisableCommentsStore(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        /** @var Post */
        $post = Post::makeFactory()->active()->publish()->withUser()->notCommentable()->create();

        Auth::login($user);

        $response = $this->post(route('web.comment.post.store', [$post->id]), [
            'content' => '<b>Komentarz</b> zostal dodany. <script>dsdasd</script>',
            'g-recaptcha-response' => 'dsadasd'
        ]);

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
        $response->assertSeeText('disabled for this post', false);
    }

    public function testRootCommentPostStore(): void
    {
        $this->mock(\N1ebieski\ICore\Rules\RecaptchaV2Rule::class, function (MockInterface $mock) {
            $mock->shouldReceive('passes')->once()->andReturn(true);
        });

        /** @var User */
        $user = User::makeFactory()->user()->create();

        /** @var Post */
        $post = Post::makeFactory()->active()->publish()->commentable()->withUser()->create();

        Auth::login($user);

        $response = $this->post(route('web.comment.post.store', [$post->id]), [
            'content' => '<b>Komentarz</b> zostal dodany. <script>dsdasd</script>',
            'g-recaptcha-response' => 'dsadasd'
        ]);

        $response->assertOk()->assertJsonStructure(['view']);

        /** @var JsonResponse */
        $baseResponse = $response->baseResponse;

        $this->assertStringContainsString(
            'Komentarz zostal dodany. dsdasd',
            $baseResponse->getData()->view
        );

        $this->assertDatabaseHas('comments', [
            'content' => 'Komentarz zostal dodany. dsdasd',
            'parent_id' => null,
            'model_id' => $post->id,
            'model_type' => $post->getMorphClass()
        ]);
    }

    public function testChildrenNoexistCommentPostStore(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        /** @var Post */
        $post = Post::makeFactory()->active()->publish()->commentable()->withUser()->create();

        Auth::login($user);

        $response = $this->post(route('web.comment.post.store', [$post->id]), [
            'content' => '<b>Komentarz</b> zostal dodany. <script>dsdasd</script>',
            'parent_id' => 3894839
        ]);

        $response->assertSessionHasErrors(['parent_id']);
    }

    public function testChildrenCommentPostStore(): void
    {
        $this->mock(\N1ebieski\ICore\Rules\RecaptchaV2Rule::class, function (MockInterface $mock) {
            $mock->shouldReceive('passes')->once()->andReturn(true);
        });

        /** @var User */
        $user = User::makeFactory()->user()->create();

        /** @var Post */
        $post = Post::makeFactory()->active()->publish()->commentable()->withUser()->create();

        /** @var Comment */
        $parent = Comment::makeFactory()->active()->withUser()->for($post, 'morph')->create();

        Auth::login($user);

        $response = $this->post(route('web.comment.post.store', [$post->id]), [
            'content' => '<b>Komentarz</b> zostal dodany. <script>dsdasd</script>',
            'parent_id' => $parent->id,
            'g-recaptcha-response' => 'dsadasd'
        ]);

        $response->assertOk()->assertJsonStructure(['view']);

        /** @var JsonResponse */
        $baseResponse = $response->baseResponse;

        $this->assertStringContainsString(
            'Komentarz zostal dodany. dsdasd',
            $baseResponse->getData()->view
        );

        $this->assertDatabaseHas('comments', [
            'content' => 'Komentarz zostal dodany. dsdasd',
            'parent_id' => $parent->id,
            'model_id' => $post->id,
            'model_type' => $post->getMorphClass()
        ]);
    }

    public function testCommentEditAsGuest(): void
    {
        $response = $this->get(route('web.comment.edit', [99]));

        $response->assertRedirect(route('login'));
    }

    public function testNoexistCommentEdit(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        $response = $this->get(route('web.comment.edit', [9999]));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testForeignCommentEdit(): void
    {
        /** @var Post */
        $post = Post::makeFactory()->active()->publish()->commentable()->withUser()->create();

        /** @var Comment */
        $comment = Comment::makeFactory()->active()->withUser()->for($post, 'morph')->create();

        /** @var User */
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        $response = $this->get(route('web.comment.edit', [$comment->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testCommentEdit(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        /** @var Post */
        $post = Post::makeFactory()->active()->publish()->commentable()->withUser()->create();

        /** @var Comment */
        $comment = Comment::makeFactory()->active()->for($post, 'morph')->for($user)->create();

        Auth::login($user);

        $response = $this->get(route('web.comment.edit', [$comment->id]));

        $response->assertOk()->assertJsonStructure(['view']);

        /** @var JsonResponse */
        $baseResponse = $response->baseResponse;

        $this->assertStringContainsString(
            route('web.comment.update', [$comment->id]),
            $baseResponse->getData()->view
        );
        $this->assertStringContainsString(
            $comment->content,
            $baseResponse->getData()->view
        );
    }

    public function testCommentUpdateAsGuest(): void
    {
        $response = $this->put(route('web.comment.update', [99]), []);

        $response->assertRedirect(route('login'));
    }

    public function testNoexistCommentUpdate(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        $response = $this->put(route('web.comment.update', [9999]), []);

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testForeignCommentUpdate(): void
    {
        /** @var Post */
        $post = Post::makeFactory()->active()->publish()->commentable()->withUser()->create();

        /** @var Comment */
        $comment = Comment::makeFactory()->active()->withUser()->for($post, 'morph')->create();

        /** @var User */
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        $response = $this->put(route('web.comment.update', [$comment->id]), [
            'content' => 'Hdsjdhsjdsj'
        ]);

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testCommentUpdateValidationFail(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        /** @var Post */
        $post = Post::makeFactory()->active()->publish()->commentable()->withUser()->create();

        /** @var Comment */
        $comment = Comment::makeFactory()->active()->for($post, 'morph')->for($user)->create();

        Auth::login($user);

        $response = $this->put(route('web.comment.update', [$comment->id]), [
            'content' => ''
        ]);

        $response->assertSessionHasErrors(['content']);
    }

    public function testCommentUpdate(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        /** @var Post */
        $post = Post::makeFactory()->active()->publish()->commentable()->withUser()->create();

        /** @var Comment */
        $comment = Comment::makeFactory()->active()->for($post, 'morph')->for($user)->create();

        Auth::login($user);

        $response = $this->put(route('web.comment.update', [$comment->id]), [
            'content' => '<b>Komentarz</b> został zaktualizowany. <script>dsadad</script>'
        ]);

        $response->assertOk()->assertJsonStructure(['view']);

        /** @var JsonResponse */
        $baseResponse = $response->baseResponse;

        $this->assertStringContainsString(
            'Komentarz został zaktualizowany. dsadad',
            $baseResponse->getData()->view
        );

        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
            'content' => 'Komentarz został zaktualizowany. dsadad'
        ]);
    }
}
