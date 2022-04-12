<?php

namespace N1ebieski\ICore\Tests\Feature\Admin;

use Tests\TestCase;
use N1ebieski\ICore\Models\Post;
use N1ebieski\ICore\Models\User;
use Illuminate\Support\Facades\Auth;
use N1ebieski\ICore\Models\Comment\Post\Comment;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CommentTest extends TestCase
{
    use DatabaseTransactions;

    public function testCommentPostIndexAsGuest()
    {
        $response = $this->get(route('admin.comment.post.index'));

        $response->assertRedirect(route('login'));
    }

    public function testCommentPostIndexWithoutPermission()
    {
        $user = User::factory()->create();

        Auth::login($user);

        $response = $this->get(route('admin.comment.post.index'));

        $response->assertStatus(403);
    }

    public function testCommentPostIndexPaginate()
    {
        $user = User::factory()->admin()->create();

        $post = Post::factory()->active()->commentable()->publish()->withUser()->create();

        $comment = Comment::factory()->count(50)->active()->withUser()->for($post, 'morph')->create();

        Auth::login($user);

        $response = $this->get(route('admin.comment.post.index', [
            'page' => 2,
            'filter' => [
                'orderby' => 'created_at|desc'
            ]
        ]));

        $response->assertViewIs('icore::admin.comment.index');
        $response->assertSee('class="pagination"', false);
        $response->assertSeeInOrder([$comment[30]->title, $comment[30]->shortContent], false);
    }

    public function testCommentShowAsGuest()
    {
        $response = $this->get(route('admin.comment.show', [32]));

        $response->assertRedirect(route('login'));
    }

    public function testCommentShowWithoutPermission()
    {
        $user = User::factory()->create();

        $post = Post::factory()->active()->commentable()->publish()->withUser()->create();

        $comment = Comment::factory()->active()->withUser()->for($post, 'morph')->create();

        Auth::login($user);

        $response = $this->get(route('admin.comment.show', [$comment->id]));

        $response->assertStatus(403);
    }

    public function testNoexistCommentShow()
    {
        $user = User::factory()->admin()->create();

        Auth::login($user);

        $response = $this->get(route('admin.comment.show', [4343434]));

        $response->assertStatus(404);
    }

    public function testCommentShow()
    {
        $user = User::factory()->admin()->create();

        $post = Post::factory()->active()->commentable()->publish()->withUser()->create();

        $parent = Comment::factory()->active()->withUser()->for($post, 'morph')->create();

        $comment = Comment::factory()->active()->withUser()->for($post, 'morph')->create([
            'parent_id' => $parent->id
        ]);

        Auth::login($user);

        $response = $this->get(route('admin.comment.show', [$comment->id]));

        $response->assertOk()->assertJsonStructure(['success', 'view']);

        $this->assertStringContainsString($parent->content, $response->getData()->view);
        $this->assertStringContainsString($comment->content, $response->getData()->view);
    }

    public function testCommentPostCreateAsGuest()
    {
        $response = $this->get(route('admin.comment.post.create', [99]));

        $response->assertRedirect(route('login'));
    }

    public function testCommentNoexistPostCreate()
    {
        $user = User::factory()->create();

        Auth::login($user);

        $response = $this->get(route('admin.comment.post.create', [9999]));

        $response->assertStatus(404);

        $this->assertTrue(Auth::check());
    }

    public function testCommentPostCreateWithoutPermission()
    {
        $user = User::factory()->create();

        $post = Post::factory()->active()->commentable()->publish()->withUser()->create();

        Auth::login($user);

        $response = $this->get(route('admin.comment.post.create', [$post->id]));

        $response->assertStatus(403);

        $this->assertTrue(Auth::check());
    }

    public function testChildrenCommentPostCreate()
    {
        $user = User::factory()->admin()->create();

        $post = Post::factory()->active()->commentable()->publish()->withUser()->create();

        $parent = Comment::factory()->active()->withUser()->for($post, 'morph')->create();

        Auth::login($user);

        $response = $this->get(route('admin.comment.post.create', [$post->id, 'parent_id' => $parent->id]));

        $response->assertOk()->assertJsonStructure(['success', 'view']);

        $this->assertStringContainsString(route('admin.comment.post.store', [$post->id]), $response->getData()->view);
        $this->assertTrue(Auth::check());
    }

    public function testCommentPostStoreAsGuest()
    {
        $response = $this->post(route('admin.comment.post.store', [99]), []);

        $response->assertRedirect(route('login'));
    }

    public function testCommentNoexistPostStore()
    {
        $user = User::factory()->create();

        Auth::login($user);

        $response = $this->post(route('admin.comment.post.store', [9999]), []);

        $response->assertStatus(404);

        $this->assertTrue(Auth::check());
    }

    public function testCommentPostStoreWithoutPermission()
    {
        $user = User::factory()->create();

        $post = Post::factory()->active()->commentable()->publish()->withUser()->create();

        Auth::login($user);

        $response = $this->post(route('admin.comment.post.store', [$post->id]), []);

        $response->assertStatus(403);

        $this->assertTrue(Auth::check());
    }

    public function testChildrenNoexistCommentPostStore()
    {
        $user = User::factory()->admin()->create();

        $post = Post::factory()->active()->commentable()->publish()->withUser()->create();

        Auth::login($user);

        $response = $this->post(route('admin.comment.post.store', [$post->id]), [
            'content' => '<b>Komentarz</b> zostal dodany. <script>dsdasd</script>',
            'parent_id' => 3894839
        ]);

        $response->assertSessionHasErrors(['parent_id']);

        $this->assertTrue(Auth::check());
    }

    public function testChildrenCommentPostStore()
    {
        $user = User::factory()->admin()->create();

        $post = Post::factory()->active()->commentable()->publish()->withUser()->create();

        $parent = Comment::factory()->active()->withUser()->for($post, 'morph')->create();

        Auth::login($user);

        $response = $this->post(route('admin.comment.post.store', [$post->id]), [
            'content' => '<b>Komentarz</b> zostal dodany. <script>dsdasd</script>',
            'parent_id' => $parent->id
        ]);

        $response->assertOk()->assertJsonStructure(['success', 'view']);

        $this->assertStringContainsString('Komentarz zostal dodany. dsdasd', $response->getData()->view);
        $this->assertDatabaseHas('comments', [
            'content' => 'Komentarz zostal dodany. dsdasd',
            'parent_id' => $parent->id,
            'model_id' => $post->id,
            'model_type' => 'N1ebieski\\ICore\\Models\\Post'
        ]);
        $this->assertTrue(Auth::check());
    }

    public function testCommentEditAsGuest()
    {
        $response = $this->get(route('admin.comment.edit', [99]));

        $response->assertRedirect(route('login'));
    }

    public function testNoexistCommentEdit()
    {
        $user = User::factory()->create();

        Auth::login($user);

        $response = $this->get(route('admin.comment.edit', [9999]));

        $response->assertStatus(404);

        $this->assertTrue(Auth::check());
    }

    public function testCommentEditWithoutPermission()
    {
        $user = User::factory()->create();

        $post = Post::factory()->active()->commentable()->publish()->withUser()->create();

        $comment = Comment::factory()->active()->withUser()->for($post, 'morph')->create();

        Auth::login($user);

        $response = $this->get(route('admin.comment.edit', [$comment->id]));

        $response->assertStatus(403);

        $this->assertTrue(Auth::check());
    }

    public function testCommentEdit()
    {
        $user = User::factory()->admin()->create();

        $post = Post::factory()->active()->commentable()->publish()->withUser()->create();

        $comment = Comment::factory()->active()->withUser()->for($post, 'morph')->create();

        Auth::login($user);

        $response = $this->get(route('admin.comment.edit', [$comment->id]));

        $response->assertOk()->assertJsonStructure(['success', 'view']);

        $this->assertStringContainsString(route('admin.comment.update', [$comment->id]), $response->getData()->view);
        $this->assertStringContainsString($comment->content, $response->getData()->view);

        $this->assertTrue(Auth::check());
    }

    public function testCommentUpdateAsGuest()
    {
        $response = $this->put(route('admin.comment.update', [99]), []);

        $response->assertRedirect(route('login'));
    }

    public function testNoexistCommentUpdate()
    {
        $user = User::factory()->create();

        Auth::login($user);

        $response = $this->put(route('admin.comment.update', [9999]), []);

        $response->assertStatus(404);

        $this->assertTrue(Auth::check());
    }

    public function testCommentUpdateWithoutPermission()
    {
        $user = User::factory()->create();

        $post = Post::factory()->active()->commentable()->publish()->withUser()->create();

        $comment = Comment::factory()->active()->withUser()->for($post, 'morph')->create();

        Auth::login($user);

        $response = $this->put(route('admin.comment.update', [$comment->id]), []);

        $response->assertStatus(403);

        $this->assertTrue(Auth::check());
    }

    public function testCommentUpdateValidationFail()
    {
        $user = User::factory()->admin()->create();

        $post = Post::factory()->active()->commentable()->publish()->withUser()->create();

        $comment = Comment::factory()->active()->withUser()->for($post, 'morph')->create();

        Auth::login($user);

        $response = $this->put(route('admin.comment.update', [$comment->id]), [
            'content' => ''
        ]);

        $response->assertSessionHasErrors(['content']);

        $this->assertTrue(Auth::check());
    }

    public function testCommentUpdate()
    {
        $user = User::factory()->admin()->create();

        $post = Post::factory()->active()->commentable()->publish()->withUser()->create();

        $comment = Comment::factory()->active()->withUser()->for($post, 'morph')->create();

        Auth::login($user);

        $response = $this->put(route('admin.comment.update', [$comment->id]), [
            'content' => '<b>Komentarz</b> został zaktualizowany. <script>dsadad</script>'
        ]);

        $response->assertOk()->assertJsonStructure(['success', 'view']);

        $this->assertStringContainsString('Komentarz został zaktualizowany. dsadad', $response->getData()->view);
        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
            'content' => 'Komentarz został zaktualizowany. dsadad'
        ]);
        $this->assertTrue(Auth::check());
    }

    public function testCommentUpdateStatusAsGuest()
    {
        $response = $this->patch(route('admin.comment.update_status', [43]));

        $response->assertRedirect(route('login'));
    }

    public function testCommentUpdateStatusWithoutPermission()
    {
        $user = User::factory()->create();

        $post = Post::factory()->active()->commentable()->publish()->withUser()->create();

        $comment = Comment::factory()->active()->withUser()->for($post, 'morph')->create();

        Auth::login($user);

        $response = $this->patch(route('admin.comment.update_status', [$comment->id]));

        $response->assertStatus(403);
    }

    public function testNoexistCommentUpdateStatus()
    {
        $user = User::factory()->admin()->create();

        Auth::login($user);

        $response = $this->patch(route('admin.comment.update_status', [2327382]));

        $response->assertStatus(404);
    }

    public function testCommentUpdateStatusValidationFail()
    {
        $user = User::factory()->admin()->create();

        $post = Post::factory()->active()->commentable()->publish()->withUser()->create();

        $comment = Comment::factory()->active()->withUser()->for($post, 'morph')->create();

        Auth::login($user);

        $response = $this->patch(route('admin.comment.update_status', [$comment->id]), [
            'status' => 323,
        ]);

        $response->assertSessionHasErrors(['status']);

        $this->assertTrue(Auth::check());
    }

    public function testCommentUpdateStatus()
    {
        $user = User::factory()->admin()->create();

        $post = Post::factory()->active()->commentable()->publish()->withUser()->create();

        $comment = Comment::factory()->active()->withUser()->for($post, 'morph')->create();

        Auth::login($user);

        $response = $this->patch(route('admin.comment.update_status', [$comment->id]), [
            'status' => 0,
        ]);

        $response->assertOk()->assertJsonStructure(['success', 'ancestors', 'descendants']);

        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
            'status' => 0,
        ]);
        $this->assertTrue(Auth::check());
    }

    public function testCommentUpdateCensoredAsGuest()
    {
        $response = $this->patch(route('admin.comment.update_censored', [43]));

        $response->assertRedirect(route('login'));
    }

    public function testCommentUpdateCensoredWithoutPermission()
    {
        $user = User::factory()->create();

        $post = Post::factory()->active()->commentable()->publish()->withUser()->create();

        $comment = Comment::factory()->active()->withUser()->for($post, 'morph')->create();

        Auth::login($user);

        $response = $this->patch(route('admin.comment.update_censored', [$comment->id]));

        $response->assertStatus(403);
    }

    public function testNoexistCommentUpdateCensored()
    {
        $user = User::factory()->admin()->create();

        Auth::login($user);

        $response = $this->patch(route('admin.comment.update_censored', [43423424]));

        $response->assertStatus(404);
    }

    public function testCommentUpdateCensoredValidationFail()
    {
        $user = User::factory()->admin()->create();

        $post = Post::factory()->active()->commentable()->publish()->withUser()->create();

        $comment = Comment::factory()->active()->withUser()->for($post, 'morph')->create();

        Auth::login($user);

        $response = $this->patch(route('admin.comment.update_censored', [$comment->id]), [
            'censored' => 323,
        ]);

        $response->assertSessionHasErrors(['censored']);

        $this->assertTrue(Auth::check());
    }

    public function testCommentUpdateCensored()
    {
        $user = User::factory()->admin()->create();

        $post = Post::factory()->active()->commentable()->publish()->withUser()->create();

        $comment = Comment::factory()->active()->withUser()->for($post, 'morph')->create();

        Auth::login($user);

        $response = $this->patch(route('admin.comment.update_censored', [$comment->id]), [
            'censored' => 1,
        ]);

        $response->assertOk()->assertJsonStructure(['success', 'view']);

        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
            'censored' => 1,
        ]);
        $this->assertTrue(Auth::check());
    }

    public function testCommentDestroyAsGuest()
    {
        $response = $this->delete(route('admin.comment.destroy', [43]));

        $response->assertRedirect(route('login'));
    }

    public function testCommentDestroyWithoutPermission()
    {
        $user = User::factory()->create();

        $post = Post::factory()->active()->commentable()->publish()->withUser()->create();

        $comment = Comment::factory()->active()->withUser()->for($post, 'morph')->create();

        Auth::login($user);

        $response = $this->delete(route('admin.comment.destroy', [$comment->id]));

        $response->assertStatus(403);
    }

    public function testNoexistCommentDestroy()
    {
        $user = User::factory()->admin()->create();

        Auth::login($user);

        $response = $this->delete(route('admin.comment.destroy', [2327382]));

        $response->assertStatus(404);
    }

    public function testCommentDestroy()
    {
        $user = User::factory()->admin()->create();

        $post = Post::factory()->active()->commentable()->publish()->withUser()->create();

        $comment = Comment::factory()->active()->withUser()->for($post, 'morph')->create();

        Auth::login($user);

        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
        ]);

        $response = $this->delete(route('admin.comment.destroy', [$comment->id]), []);
        $response->assertOk()->assertJsonStructure(['success']);

        $this->assertDatabaseMissing('comments', [
            'id' => $comment->id,
        ]);
        $this->assertTrue(Auth::check());
    }

    public function testCommentDestroyGlobalAsGuest()
    {
        $response = $this->delete(route('admin.comment.destroy_global'), []);

        $response->assertRedirect(route('login'));
    }

    public function testCommentDestroyGlobalWithoutPermission()
    {
        $user = User::factory()->create();

        Auth::login($user);

        $response = $this->delete(route('admin.comment.destroy_global'), []);

        $response->assertStatus(403);
    }

    public function testCommentDestroyGlobalValidationFail()
    {
        $user = User::factory()->admin()->create();

        Auth::login($user);

        $response = $this->delete(route('admin.comment.destroy_global', [
            'dasdas',
            'dada'
        ]));

        $response->assertSessionHasErrors(['select']);
    }

    public function testCommentDestroyGlobal()
    {
        $user = User::factory()->admin()->create();

        $post = Post::factory()->active()->commentable()->publish()->withUser()->create();

        $comment = Comment::factory()->count(10)->withUser()->for($post, 'morph')->create();

        Auth::login($user);

        $this->get(route('admin.comment.post.index'));

        $select = collect($comment)->pluck('id')->take(5)->toArray();

        $response = $this->delete(route('admin.comment.destroy_global'), [
            'select' => $select,
        ]);

        $response->assertRedirect(route('admin.comment.post.index'));
        $response->assertSessionHas('success');

        $deleted = Comment::whereIn('id', $select)->count();

        $this->assertTrue($deleted === 0);
        $this->assertTrue(Auth::check());
    }
}
