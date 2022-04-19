<?php

namespace N1ebieski\ICore\Tests\Feature\Web;

use Tests\TestCase;
use N1ebieski\ICore\Models\User;
use Illuminate\Support\Facades\Auth;
use N1ebieski\ICore\Models\Rating\Rating;
use Illuminate\Http\Response as HttpResponse;
use N1ebieski\ICore\Models\Comment\Post\Comment;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class RatingTest extends TestCase
{
    use DatabaseTransactions;

    public function testRatingCommentRateAsGuest()
    {
        $response = $this->get(route('web.rating.comment.rate', [99]), []);

        $response->assertRedirect(route('login'));
    }

    public function testRatingNoexistCommentRate()
    {
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        $response = $this->get(route('web.rating.comment.rate', [9999]), []);

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testRatingCommentRateValidationFail()
    {
        $user = User::makeFactory()->user()->create();

        $comment = Comment::makeFactory()->active()->withUser()->withMorph()->create();

        Auth::login($user);

        $response = $this->get(route('web.rating.comment.rate', [$comment->id, 'rating' => 2323]));

        $response->assertSessionHasErrors(['rating']);
    }

    public function testRatingCommentRateCreate()
    {
        $user = User::makeFactory()->user()->create();

        $comment = Comment::makeFactory()->active()->withMorph()->for($user)->create();

        Auth::login($user);

        $this->get(route('web.rating.comment.rate', [$comment->id, 'rating' => 1]));

        $this->assertDatabaseHas('ratings', [
            'user_id' => $user->id,
            'model_id' => $comment->id,
            'model_type' => $comment->getMorphClass()
        ]);
    }

    public function testRatingCommentRateDelete()
    {
        $user = User::makeFactory()->user()->create();

        $comment = Comment::makeFactory()->active()->withUser()->withMorph()->create();

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

    public function testRatingCommentRateUpdate()
    {
        $user = User::makeFactory()->user()->create();

        $comment = Comment::makeFactory()->active()->withUser()->withMorph()->create();

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
