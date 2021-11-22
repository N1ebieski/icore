<?php

namespace N1ebieski\ICore\Tests\Feature\Admin;

use Tests\TestCase;
use Faker\Factory as Faker;
use N1ebieski\ICore\Models\Post;
use N1ebieski\ICore\Models\User;
use Illuminate\Support\Facades\Auth;
use N1ebieski\ICore\Models\Report\Report;
use N1ebieski\ICore\Models\Comment\Post\Comment;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ReportTest extends TestCase
{
    use DatabaseTransactions;

    public function testReportCommentShowAsGuest()
    {
        $response = $this->get(route('admin.report.comment.show', [32]));

        $response->assertRedirect(route('login'));
    }

    public function testReportCommentShowWithoutPermission()
    {
        $user = factory(User::class)->create();

        $post = factory(Post::class)->states(['active', 'commentable', 'publish', 'with_user'])->create();

        $comment = factory(Comment::class)->states(['active', 'with_user'])->make();
        $comment->morph()->associate($post)->save();

        Auth::login($user, true);

        $response = $this->get(route('admin.report.comment.show', [$comment->id]));

        $response->assertStatus(403);
    }

    public function testReportNoexistCommentShow()
    {
        $user = factory(User::class)->states('admin')->create();

        Auth::login($user, true);

        $response = $this->get(route('admin.report.comment.show', [4343434]));

        $response->assertStatus(404);
    }

    public function testReportCommentShow()
    {
        $user = factory(User::class)->states('admin')->create();

        $post = factory(Post::class)->states(['active', 'commentable', 'publish', 'with_user'])->create();

        $comment = factory(Comment::class)->states(['active', 'with_user'])->make();
        $comment->morph()->associate($post)->save();

        $report = factory(Report::class, 10)
            ->states('with_user')
            ->make()
            ->each(function ($report) use ($comment) {
                $report->morph()->associate($comment)->save();
            });

        Auth::login($user, true);

        $response = $this->get(route('admin.report.comment.show', [$comment->id]));

        $response->assertOk()->assertJsonStructure(['success', 'view']);
        $this->assertStringContainsString($report[6]->content, $response->getData()->view);
    }

    public function testReportCommentClearAsGuest()
    {
        $response = $this->delete(route('admin.report.comment.clear', [32]));

        $response->assertRedirect(route('login'));
    }

    public function testReportCommentClearWithoutPermission()
    {
        $user = factory(User::class)->create();

        $post = factory(Post::class)->states(['active', 'commentable', 'publish', 'with_user'])->create();

        $comment = factory(Comment::class)->states(['active', 'with_user'])->make();
        $comment->morph()->associate($post)->save();

        Auth::login($user, true);

        $response = $this->delete(route('admin.report.comment.clear', [$comment->id]));

        $response->assertStatus(403);
    }

    public function testReportNoexistCommentClear()
    {
        $user = factory(User::class)->states('admin')->create();

        Auth::login($user, true);

        $response = $this->delete(route('admin.report.comment.clear', [4343434]));

        $response->assertStatus(404);
    }

    public function testReportCommentClear()
    {
        $user = factory(User::class)->states('admin')->create();

        $post = factory(Post::class)->states(['active', 'commentable', 'publish', 'with_user'])->create();

        $comment = factory(Comment::class)->states(['active', 'with_user'])->make();
        $comment->morph()->associate($post)->save();

        $report = factory(Report::class, 10)
            ->states('with_user')
            ->make()
            ->each(function ($report) use ($comment) {
                $report->morph()->associate($comment)->save();
            });

        Auth::login($user, true);

        $response = $this->delete(route('admin.report.comment.clear', [$comment->id]));

        $response->assertOk()->assertJsonStructure(['success', 'view']);

        $deleted = Report::whereIn('id', collect($report)->pluck('id')->toArray())->count();

        $this->assertTrue($deleted === 0);
    }
}
