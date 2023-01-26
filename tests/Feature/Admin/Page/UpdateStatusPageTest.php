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

namespace N1ebieski\ICore\Tests\Feature\Admin\Page;

use Tests\TestCase;
use N1ebieski\ICore\Models\User;
use Illuminate\Support\Facades\Auth;
use N1ebieski\ICore\Models\Page\Page;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UpdateStatusPageTest extends TestCase
{
    use DatabaseTransactions;

    public function testUpdateStatusAsGuest(): void
    {
        $response = $this->patch(route('admin.page.update_status', [43]));

        $response->assertRedirect(route('login'));
    }

    public function testUpdateStatusWithoutPermission(): void
    {
        /**
         * @var User
         */
        $user = User::makeFactory()->create();

        /**
         * @var Page
         */
        $page = Page::makeFactory()->active()->withUser()->create();

        Auth::login($user);

        $response = $this->patch(route('admin.page.update_status', [$page->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testUpdateStatusNoExist(): void
    {
        /**
         * @var User
         */
        $user = User::makeFactory()->admin()->create();

        Auth::login($user);

        $response = $this->patch(route('admin.page.update_status', [2327382]));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testUpdateStatusValidationFail(): void
    {
        /**
         * @var User
         */
        $user = User::makeFactory()->admin()->create();

        /**
         * @var Page
         */
        $page = Page::makeFactory()->active()->withUser()->create();

        Auth::login($user);

        $response = $this->patch(route('admin.page.update_status', [$page->id]), [
            'status' => 323,
        ]);

        $response->assertSessionHasErrors(['status']);
    }

    public function testUpdateStatus(): void
    {
        /**
         * @var User
         */
        $user = User::makeFactory()->admin()->create();

        /**
         * @var Page
         */
        $page = Page::makeFactory()->active()->withUser()->create();

        Auth::login($user);

        $response = $this->patch(route('admin.page.update_status', [$page->id]), [
            'status' => 0,
        ]);

        $response->assertOk()->assertJsonStructure(['ancestors', 'descendants']);

        $this->assertDatabaseHas('pages', [
            'id' => $page->id,
            'status' => 0,
        ]);
    }
}
