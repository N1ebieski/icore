<?php

namespace N1ebieski\ICore\Tests\Feature\Web;

use Tests\TestCase;
use N1ebieski\ICore\Models\User;
use Illuminate\Support\Facades\Auth;
use N1ebieski\ICore\Models\Rating\Rating;
use N1ebieski\ICore\Models\Comment\Comment;
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
        $user = factory(User::class)->states('user')->create();

        Auth::login($user, true);

        $response = $this->get(route('web.rating.comment.rate', [9999]), []);

        $response->assertStatus(404);

        $this->assertTrue(Auth::check());
    }

    public function testRatingCommentRateValidationFail()
    {
        $user = factory(User::class)->states('user')->create();

        $comment = factory(Comment::class)->states(['active', 'with_user', 'with_post'])->create();

        Auth::login($user, true);

        $response = $this->get(route('web.rating.comment.rate', [$comment->id, 'rating' => 2323]));

        $response->assertSessionHasErrors(['rating']);

        $this->assertTrue(Auth::check());
    }

    public function testRatingCommentRateCreate()
    {
        $user = factory(User::class)->states('user')->create();

        $comment = factory(Comment::class)->states(['active', 'with_post'])->make();
        $comment->user()->associate($user)->save();

        Auth::login($user, true);

        $response = $this->get(route('web.rating.comment.rate', [$comment->id, 'rating' => 1]));

        $this->assertDatabaseHas('ratings', [
            'user_id' => $user->id,
            'model_id' => $comment->id,
            'model_type' => 'N1ebieski\\ICore\\Models\\Comment\\Comment'
        ]);

        $this->assertTrue(Auth::check());
    }

    public function testRatingCommentRateDelete()
    {
        $user = factory(User::class)->states('user')->create();

        $comment = factory(Comment::class)->states(['active', 'with_user', 'with_post'])->create();

        $rating = factory(Rating::class)->states('one')->make();
        $rating->user()->associate($user);
        $rating->morph()->associate($comment);
        $rating->save();

        $this->assertDatabaseHas('ratings', [
            'id' => $rating->id
        ]);

        Auth::login($user, true);

        $response = $this->get(route('web.rating.comment.rate', [$comment->id, 'rating' => 1]));

        $this->assertDatabaseMissing('ratings', [
            'id' => $rating->id
        ]);

        $this->assertTrue(Auth::check());
    }

    public function testRatingCommentRateUpdate()
    {
        $user = factory(User::class)->states('user')->create();

        $comment = factory(Comment::class)->states(['active', 'with_user', 'with_post'])->create();

        $rating = factory(Rating::class)->states('one')->make();
        $rating->user()->associate($user);
        $rating->morph()->associate($comment);
        $rating->save();

        $this->assertDatabaseHas('ratings', [
            'id' => $rating->id
        ]);

        Auth::login($user, true);

        $response = $this->get(route('web.rating.comment.rate', [$comment->id, 'rating' => -1]));

        $this->assertDatabaseHas('ratings', [
            'id' => $rating->id,
            'user_id' => $user->id,
            'model_id' => $comment->id,
            'model_type' => 'N1ebieski\\ICore\\Models\\Comment\\Comment',
            'rating' => -1
        ]);

        $this->assertTrue(Auth::check());
    }
}
