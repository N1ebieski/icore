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

namespace N1ebieski\ICore\Tests\Feature\Web\Comment;

use Tests\TestCase;
use N1ebieski\ICore\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UpdateCommentTest extends TestCase
{
    use DatabaseTransactions;

    public function testEditAsGuest(): void
    {
        $response = $this->get(route('web.comment.edit', [99]));

        $response->assertRedirect(route('login'));
    }

    public function testEditNoExist(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        $response = $this->get(route('web.comment.edit', [9999]));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testUpdateAsGuest(): void
    {
        $response = $this->put(route('web.comment.update', [99]), []);

        $response->assertRedirect(route('login'));
    }

    public function testUpdateNoExist(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        $response = $this->put(route('web.comment.update', [9999]), []);

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }
}
