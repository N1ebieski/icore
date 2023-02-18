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

namespace N1ebieski\ICore\Tests\Feature\Web\Report\Comment;

use Tests\TestCase;
use Mockery\MockInterface;
use N1ebieski\ICore\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response as HttpResponse;
use N1ebieski\ICore\Models\Comment\Post\Comment;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CreateReportTest extends TestCase
{
    use DatabaseTransactions;

    public function testCreateNoExist(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        $response = $this->get(route('web.report.comment.create', [9999]));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testCreateInactive(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        /** @var Comment */
        $comment = Comment::makeFactory()->inactive()->withUser()->withMorph()->create();

        Auth::login($user);

        $response = $this->get(route('web.report.comment.create', [$comment->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testCreate(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        /** @var Comment */
        $comment = Comment::makeFactory()->active()->withUser()->withMorph()->create();

        Auth::login($user);

        $response = $this->get(route('web.report.comment.create', [$comment->id]));

        $response->assertOk()->assertJsonStructure(['view']);

        /** @var JsonResponse */
        $baseResponse = $response->baseResponse;

        $this->assertStringContainsString(route('web.report.comment.store', [$comment->id]), $baseResponse->getData()->view);
    }

    public function testStoreNoExist(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        $response = $this->post(route('web.report.comment.store', [9999]), []);

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testStoreInactive(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        /** @var Comment */
        $comment = Comment::makeFactory()->inactive()->withUser()->withMorph()->create();

        Auth::login($user);

        $response = $this->post(route('web.report.comment.store', [$comment->id]), []);

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testStoreValidationFail(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        /** @var Comment */
        $comment = Comment::makeFactory()->active()->withUser()->withMorph()->create();

        Auth::login($user);

        $response = $this->post(route('web.report.comment.store', [$comment->id]), [
            'content' => '',
        ]);

        $response->assertSessionHasErrors(['content']);
    }

    public function testStoreAsGuest(): void
    {
        $this->mock(\N1ebieski\ICore\Rules\RecaptchaV2Rule::class, function (MockInterface $mock) {
            $mock->shouldReceive('passes')->once()->andReturn(true);
        });

        /** @var Comment */
        $comment = Comment::makeFactory()->active()->withUser()->withMorph()->create();

        $response = $this->post(route('web.report.comment.store', [$comment->id]), [
            'content' => 'Ten <b>komentarz</b> jest zły. <script>Usunąć!</script>',
            'g-recaptcha-response' => 'dsadasd'
        ]);

        $response->assertOk()->assertJson([
            'success' => trans('icore::reports.success.store')
        ]);

        $this->assertDatabaseHas('reports', [
            'user_id' => null,
            'model_id' => $comment->id,
            'model_type' => $comment->getMorphClass(),
            'content' => 'Ten komentarz jest zły. Usunąć!'
        ]);
    }

    public function testStoreAsUser(): void
    {
        $this->mock(\N1ebieski\ICore\Rules\RecaptchaV2Rule::class, function (MockInterface $mock) {
            $mock->shouldReceive('passes')->once()->andReturn(true);
        });

        /** @var User */
        $user = User::makeFactory()->user()->create();

        /** @var Comment */
        $comment = Comment::makeFactory()->active()->withUser()->withMorph()->create();

        Auth::login($user);

        $response = $this->post(route('web.report.comment.store', [$comment->id]), [
            'content' => 'Ten <b>komentarz</b> jest zły. <script>Usunąć!</script>',
            'g-recaptcha-response' => 'dsadasd'
        ]);

        $response->assertOk()->assertJson([
            'success' => trans('icore::reports.success.store')
        ]);

        $this->assertDatabaseHas('reports', [
            'user_id' => $user->id,
            'model_id' => $comment->id,
            'model_type' => $comment->getMorphClass(),
            'content' => 'Ten komentarz jest zły. Usunąć!'
        ]);
    }
}
