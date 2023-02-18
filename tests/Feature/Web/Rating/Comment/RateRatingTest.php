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

namespace N1ebieski\ICore\Tests\Feature\Web\Rating\Comment;

use Tests\TestCase;
use N1ebieski\ICore\Models\User;
use Illuminate\Support\Facades\Auth;
use N1ebieski\ICore\Models\Rating\Rating;
use Illuminate\Http\Response as HttpResponse;
use N1ebieski\ICore\Models\Comment\Post\Comment;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class RateRatingTest extends TestCase
{
    use DatabaseTransactions;

    public function testRateAsGuest(): void
    {
        $response = $this->get(route('web.rating.comment.rate', [99]), []);

        $response->assertRedirect(route('login'));
    }

    public function testRateNoExist(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        $response = $this->get(route('web.rating.comment.rate', [9999]), []);

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testRateValidationFail(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        /** @var Comment */
        $comment = Comment::makeFactory()->active()->withUser()->withMorph()->create();

        Auth::login($user);

        $response = $this->get(route('web.rating.comment.rate', [$comment->id, 'rating' => 2323]));

        $response->assertSessionHasErrors(['rating']);
    }

    public function testCreateRate(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        /** @var Comment */
        $comment = Comment::makeFactory()->active()->withMorph()->for($user)->create();

        Auth::login($user);

        $this->get(route('web.rating.comment.rate', [$comment->id, 'rating' => 1]));

        $this->assertDatabaseHas('ratings', [
            'user_id' => $user->id,
            'model_id' => $comment->id,
            'model_type' => $comment->getMorphClass()
        ]);
    }

    public function testDeleteRate(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        /** @var Comment */
        $comment = Comment::makeFactory()->active()->withUser()->withMorph()->create();

        /** @var Rating */
        $rating = Rating::makeFactory()->one()->for($user)->for($comment, 'morph')->create();

        $this->assertDatabaseHas('ratings', [
            'id' => $rating->id
        ]);

        Auth::login($user);

        $this->get(route('web.rating.comment.rate', [$comment->id, 'rating' => 1]));

        $this->assertDatabaseMissing('ratings', [
            'id' => $rating->id
        ]);
    }

    public function testUpdateRate(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        /** @var Comment */
        $comment = Comment::makeFactory()->active()->withUser()->withMorph()->create();

        /** @var Rating */
        $rating = Rating::makeFactory()->one()->for($user)->for($comment, 'morph')->create();

        $this->assertDatabaseHas('ratings', [
            'id' => $rating->id
        ]);

        Auth::login($user);

        $this->get(route('web.rating.comment.rate', [$comment->id, 'rating' => -1]));

        $this->assertDatabaseHas('ratings', [
            'id' => $rating->id,
            'user_id' => $user->id,
            'model_id' => $comment->id,
            'model_type' => $comment->getMorphClass(),
            'rating' => -1
        ]);
    }
}
