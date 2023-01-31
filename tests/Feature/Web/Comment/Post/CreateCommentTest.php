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

namespace N1ebieski\ICore\Tests\Feature\Web\Comment\Post;

use Tests\TestCase;
use Mockery\MockInterface;
use N1ebieski\ICore\Models\Post;
use N1ebieski\ICore\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use N1ebieski\ICore\Models\BanValue;
use Illuminate\Http\Response as HttpResponse;
use N1ebieski\ICore\ValueObjects\BanValue\Type;
use N1ebieski\ICore\Models\Comment\Post\Comment;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CreateCommentTest extends TestCase
{
    use DatabaseTransactions;

    public function testCreateAsGuest(): void
    {
        $response = $this->get(route('web.comment.post.create', [99]));

        $response->assertRedirect(route('login'));
    }

    public function testCreateNoExist(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        $response = $this->get(route('web.comment.post.create', [9999]));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testCreateAsBannedUser(): void
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

    public function testCreateWithBannedIp(): void
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

    public function testCreateIfCommentableIsDisable(): void
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

    public function testCreate(): void
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

    public function testStoreAsGuest(): void
    {
        $response = $this->post(route('web.comment.post.store', [99]), []);

        $response->assertRedirect(route('login'));
    }

    public function testStoreNoExist(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        $response = $this->post(route('web.comment.post.store', [9999]), []);

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testStoreRootWithBannedWord(): void
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

    public function testStoreIfCommentableIsDisable(): void
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

    public function testStoreRoot(): void
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

    public function testStoreChildrenNoExist(): void
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

    public function testStoreChildren(): void
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
}
