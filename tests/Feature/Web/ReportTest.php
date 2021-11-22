<?php

namespace N1ebieski\ICore\Tests\Feature\Web;

use Tests\TestCase;
use Faker\Factory as Faker;
use N1ebieski\ICore\Models\Post;
use N1ebieski\ICore\Models\User;
use Illuminate\Support\Facades\Auth;
use N1ebieski\ICore\Models\Comment\Comment;
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
        $user = factory(User::class)->states('user')->create();

        Auth::login($user, true);

        $response = $this->get(route('web.report.comment.create', [9999]));

        $response->assertStatus(404);

        $this->assertTrue(Auth::check());
    }

    public function testReportInactiveCommentCreate()
    {
        $user = factory(User::class)->states('user')->create();

        $comment = factory(Comment::class)->states(['inactive', 'with_user', 'with_post'])->create();

        Auth::login($user, true);

        $response = $this->get(route('web.report.comment.create', [$comment->id]));

        $response->assertStatus(403);

        $this->assertTrue(Auth::check());
    }

    public function testReportCommentCreate()
    {
        $user = factory(User::class)->states('user')->create();

        $comment = factory(Comment::class)->states(['active', 'with_user', 'with_post'])->create();

        Auth::login($user, true);

        $response = $this->get(route('web.report.comment.create', [$comment->id]));

        $response->assertOk()->assertJsonStructure(['success', 'view']);
        $this->assertStringContainsString(route('web.report.comment.store', [$comment->id]), $response->getData()->view);

        $this->assertTrue(Auth::check());
    }

    public function testReportCommentStoreAsGuest()
    {
        $response = $this->post(route('web.report.comment.store', [99]), []);

        $response->assertRedirect(route('login'));
    }

    public function testReportNoexistCommentStore()
    {
        $user = factory(User::class)->states('user')->create();

        Auth::login($user, true);

        $response = $this->post(route('web.report.comment.store', [9999]), []);

        $response->assertStatus(404);

        $this->assertTrue(Auth::check());
    }

    public function testReportInactiveCommentStore()
    {
        $user = factory(User::class)->states('user')->create();

        $comment = factory(Comment::class)->states(['inactive', 'with_user', 'with_post'])->create();

        Auth::login($user, true);

        $response = $this->post(route('web.report.comment.store', [$comment->id]), []);

        $response->assertStatus(403);

        $this->assertTrue(Auth::check());
    }

    public function testReportCommentStoreValidationFail()
    {
        $user = factory(User::class)->states('user')->create();

        $comment = factory(Comment::class)->states(['active', 'with_user', 'with_post'])->create();

        Auth::login($user, true);

        $response = $this->post(route('web.report.comment.store', [$comment->id]), [
            'content' => '',
        ]);

        $response->assertSessionHasErrors(['content']);

        $this->assertTrue(Auth::check());
    }

    public function testReportCommentStore()
    {
        $user = factory(User::class)->states('user')->create();

        $comment = factory(Comment::class)->states(['active', 'with_user', 'with_post'])->create();

        Auth::login($user, true);

        $response = $this->post(route('web.report.comment.store', [$comment->id]), [
            'content' => 'Ten <b>komentarz</b> jest zły. <script>Usunąć!</script>'
        ]);

        $response->assertOk()->assertJson([
            'success' => trans('icore::reports.success.store')
        ]);

        $this->assertDatabaseHas('reports', [
            'model_id' => $comment->id,
            'model_type' => 'N1ebieski\\ICore\\Models\\Comment\\Comment',
            'content' => 'Ten komentarz jest zły. Usunąć!'
        ]);

        $this->assertTrue(Auth::check());
    }
}
