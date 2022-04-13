<?php

namespace N1ebieski\ICore\Tests\Feature\Web;

use Tests\TestCase;
use N1ebieski\ICore\Models\User;
use Illuminate\Support\Facades\Auth;
use N1ebieski\ICore\Models\Comment\Post\Comment;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ReportTest extends TestCase
{
    use DatabaseTransactions;

    public function testReportCommentCreateAsGuest()
    {
        $response = $this->get(route('web.report.comment.create', [99]));

        $response->assertRedirect(route('login'));
    }

    public function testReportNoexistCommentCreate()
    {
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        $response = $this->get(route('web.report.comment.create', [9999]));

        $response->assertStatus(404);
    }

    public function testReportInactiveCommentCreate()
    {
        $user = User::makeFactory()->user()->create();

        $comment = Comment::makeFactory()->inactive()->withUser()->withMorph()->create();

        Auth::login($user);

        $response = $this->get(route('web.report.comment.create', [$comment->id]));

        $response->assertStatus(403);
    }

    public function testReportCommentCreate()
    {
        $user = User::makeFactory()->user()->create();

        $comment = Comment::makeFactory()->active()->withUser()->withMorph()->create();

        Auth::login($user);

        $response = $this->get(route('web.report.comment.create', [$comment->id]));

        $response->assertOk()->assertJsonStructure(['success', 'view']);

        $this->assertStringContainsString(route('web.report.comment.store', [$comment->id]), $response->getData()->view);
    }

    public function testReportCommentStoreAsGuest()
    {
        $response = $this->post(route('web.report.comment.store', [99]), []);

        $response->assertRedirect(route('login'));
    }

    public function testReportNoexistCommentStore()
    {
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        $response = $this->post(route('web.report.comment.store', [9999]), []);

        $response->assertStatus(404);
    }

    public function testReportInactiveCommentStore()
    {
        $user = User::makeFactory()->user()->create();

        $comment = Comment::makeFactory()->inactive()->withUser()->withMorph()->create();

        Auth::login($user);

        $response = $this->post(route('web.report.comment.store', [$comment->id]), []);

        $response->assertStatus(403);
    }

    public function testReportCommentStoreValidationFail()
    {
        $user = User::makeFactory()->user()->create();

        $comment = Comment::makeFactory()->active()->withUser()->withMorph()->create();

        Auth::login($user);

        $response = $this->post(route('web.report.comment.store', [$comment->id]), [
            'content' => '',
        ]);

        $response->assertSessionHasErrors(['content']);
    }

    public function testReportCommentStore()
    {
        $user = User::makeFactory()->user()->create();

        $comment = Comment::makeFactory()->active()->withUser()->withMorph()->create();

        Auth::login($user);

        $response = $this->post(route('web.report.comment.store', [$comment->id]), [
            'content' => 'Ten <b>komentarz</b> jest zły. <script>Usunąć!</script>'
        ]);

        $response->assertOk()->assertJson([
            'success' => trans('icore::reports.success.store')
        ]);

        $this->assertDatabaseHas('reports', [
            'model_id' => $comment->id,
            'model_type' => $comment->getMorphClass(),
            'content' => 'Ten komentarz jest zły. Usunąć!'
        ]);
    }
}
