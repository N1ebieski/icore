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

namespace N1ebieski\ICore\Tests\Feature\Web\Comment\Post;

use Tests\TestCase;
use N1ebieski\ICore\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\Collection;
use N1ebieski\ICore\Models\Comment\Post\Comment;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class TakeCommentTest extends TestCase
{
    use DatabaseTransactions;

    public function testTake(): void
    {
        /** @var Post */
        $post = Post::makeFactory()->active()->commentable()->withUser()->create();

        /** @var Comment */
        $parent = Comment::makeFactory()->active()->withUser()->for($post, 'morph')->create();

        /** @var Collection<Comment>|array<Comment> */
        $comments = Comment::makeFactory()->count(15)->active()->withUser()->for($post, 'morph')->create([
            'parent_id' => $parent->id
        ]);

        $response = $this->post(route('web.comment.take', [$parent->id]), [
            'filter' => [
                'except' => collect($comments)->pluck('id')->take(5)->toArray(),
                'orderby' => 'created_at|asc'
            ]
        ]);

        $response->assertOk()->assertJsonStructure(['view']);

        /** @var JsonResponse */
        $baseResponse = $response->baseResponse;

        $this->assertStringContainsString(
            route('web.comment.take', [$parent->id]),
            $baseResponse->getData()->view
        );

        $this->assertStringContainsString(
            $comments[9]->content,
            $baseResponse->getData()->view
        );
    }
}
