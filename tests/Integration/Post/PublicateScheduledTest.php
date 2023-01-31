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

namespace N1ebieski\ICore\Tests\Integration\Post;

use Tests\TestCase;
use N1ebieski\ICore\Models\Post;
use N1ebieski\ICore\Crons\PostCron;
use N1ebieski\ICore\ValueObjects\Post\Status;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PublicateScheduledTest extends TestCase
{
    use DatabaseTransactions;

    public function testQueueJob(): void
    {
        /** @var Post */
        $post = Post::makeFactory()->scheduled()->withUser()->commentable()->create();

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'status' => Status::SCHEDULED
        ]);

        // Uruchamiamy zadanie crona bezpośrednio, bo przez schedule:run ma ustalony delay
        // (np. odpala się co godzinę)
        $schedule = app()->make(PostCron::class);
        $schedule();

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'status' => Status::ACTIVE
        ]);
    }
}
