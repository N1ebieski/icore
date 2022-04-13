<?php

namespace N1ebieski\ICore\Tests\Feature\Admin;

use Tests\TestCase;
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
        $user = User::makeFactory()->create();

        $post = Post::makeFactory()->active()->commentable()->publish()->withUser()->create();

        $comment = Comment::makeFactory()->active()->withUser()->for($post, 'morph')->create();

        Auth::login($user);

        $response = $this->get(route('admin.report.comment.show', [$comment->id]));

        $response->assertStatus(403);
    }

    public function testReportNoexistCommentShow()
    {
        $user = User::makeFactory()->admin()->create();

        Auth::login($user);

        $response = $this->get(route('admin.report.comment.show', [4343434]));

        $response->assertStatus(404);
    }

    public function testReportCommentShow()
    {
        $user = User::makeFactory()->admin()->create();

        $post = Post::makeFactory()->active()->commentable()->publish()->withUser()->create();

        $comment = Comment::makeFactory()->active()->withUser()->for($post, 'morph')->create();

        $report = Report::makeFactory()->count(10)->withUser()->for($comment, 'morph')->create();

        Auth::login($user);

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
        $user = User::makeFactory()->create();

        $post = Post::makeFactory()->active()->commentable()->publish()->withUser()->create();

        $comment = Comment::makeFactory()->active()->withUser()->for($post, 'morph')->create();

        Auth::login($user);

        $response = $this->delete(route('admin.report.comment.clear', [$comment->id]));

        $response->assertStatus(403);
    }

    public function testReportNoexistCommentClear()
    {
        $user = User::makeFactory()->admin()->create();

        Auth::login($user);

        $response = $this->delete(route('admin.report.comment.clear', [4343434]));

        $response->assertStatus(404);
    }

    public function testReportCommentClear()
    {
        $user = User::makeFactory()->admin()->create();

        $post = Post::makeFactory()->active()->commentable()->publish()->withUser()->create();

        $comment = Comment::makeFactory()->active()->withUser()->for($post, 'morph')->create();

        $report = Report::makeFactory()->count(10)->withUser()->for($comment, 'morph')->create();

        Auth::login($user);

        $response = $this->delete(route('admin.report.comment.clear', [$comment->id]));

        $response->assertOk()->assertJsonStructure(['success', 'view']);

        $deleted = Report::whereIn('id', collect($report)->pluck('id')->toArray())->count();

        $this->assertTrue($deleted === 0);
    }
}
