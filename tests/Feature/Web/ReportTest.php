<?php

namespace N1ebieski\ICore\Tests\Feature\Web;

use Tests\TestCase;
use N1ebieski\ICore\Models\User;
use N1ebieski\ICore\Models\Post;
use N1ebieski\ICore\Models\Comment\Comment;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Auth;

class ReportTest extends TestCase
{
    use DatabaseTransactions;

    public function test_report_comment_create_as_guest()
    {
        $response = $this->get(route('web.report.comment.create', [99]));

        $response->assertRedirect(route('login'));
    }

    public function test_report_noexist_comment_create()
    {
        $user = factory(User::class)->states('user')->create();

        Auth::login($user, true);

        $response = $this->get(route('web.report.comment.create', [9999]));

        $response->assertStatus(404);

        $this->assertTrue(Auth::check());
    }

    public function test_report_inactive_comment_create()
    {
        $user = factory(User::class)->states('user')->create();

        $comment = factory(Comment::class)->states(['inactive', 'with_user', 'with_post'])->create();

        Auth::login($user, true);

        $response = $this->get(route('web.report.comment.create', [$comment->id]));

        $response->assertStatus(403);

        $this->assertTrue(Auth::check());
    }

    public function test_report_comment_create()
    {
        $user = factory(User::class)->states('user')->create();

        $comment = factory(Comment::class)->states(['active', 'with_user', 'with_post'])->create();

        Auth::login($user, true);

        $response = $this->get(route('web.report.comment.create', [$comment->id]));

        $response->assertOk()->assertJsonStructure(['success', 'view']);
        $this->assertStringContainsString(route('web.report.comment.store', [$comment->id]), $response->getData()->view);

        $this->assertTrue(Auth::check());
    }

    public function test_report_comment_store_as_guest()
    {
        $response = $this->post(route('web.report.comment.store', [99]), []);

        $response->assertRedirect(route('login'));
    }

    public function test_report_noexist_comment_store()
    {
        $user = factory(User::class)->states('user')->create();

        Auth::login($user, true);

        $response = $this->post(route('web.report.comment.store', [9999]), []);

        $response->assertStatus(404);

        $this->assertTrue(Auth::check());
    }

    public function test_report_inactive_comment_store()
    {
        $user = factory(User::class)->states('user')->create();

        $comment = factory(Comment::class)->states(['inactive', 'with_user', 'with_post'])->create();

        Auth::login($user, true);

        $response = $this->post(route('web.report.comment.store', [$comment->id]), []);

        $response->assertStatus(403);

        $this->assertTrue(Auth::check());
    }

    public function test_report_comment_store_validation_fail()
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

    public function test_report_comment_store()
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
