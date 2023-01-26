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
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Auth;
use N1ebieski\ICore\Models\Page\Page;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UpdatePositionPageTest extends TestCase
{
    use DatabaseTransactions;

    public function testEditPositionAsGuest(): void
    {
        $response = $this->get(route('admin.page.edit_position', [43]));

        $response->assertRedirect(route('login'));
    }

    public function testEditPositionWithoutPermission(): void
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

        $response = $this->get(route('admin.page.edit_position', [$page->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testEditPositionNoExist(): void
    {
        /**
         * @var User
         */
        $user = User::makeFactory()->admin()->create();

        Auth::login($user);

        $response = $this->get(route('admin.page.edit_position', [2327382]));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testEditPosition(): void
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

        $response = $this->get(route('admin.page.edit_position', [$page->id]));

        $response->assertOk()->assertJsonStructure(['view']);

        /**
         * @var JsonResponse
         */
        $baseResponse = $response->baseResponse;

        $this->assertStringContainsString(
            'value="' . $page->position . '"',
            $baseResponse->getData()->view
        );
        $this->assertStringContainsString(
            URL::route('admin.page.update_position', [$page->id]),
            $baseResponse->getData()->view
        );
    }

    public function testUpdatePositionAsGuest(): void
    {
        $response = $this->patch(route('admin.page.update_position', [2323]));

        $response->assertRedirect(route('login'));
    }

    public function testUpdatePositionWithoutPermission(): void
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

        $response = $this->patch(route('admin.page.update_position', [$page->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testUpdatePositionNoExist(): void
    {
        /**
         * @var User
         */
        $user = User::makeFactory()->admin()->create();

        Auth::login($user);

        $response = $this->patch(route('admin.page.update_position', [2327382]));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testUpdatePositionValidationFail(): void
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

        $response = $this->patch(route('admin.page.update_position', [$page->id]), [
            'position' => 1232
        ]);

        $response->assertSessionHasErrors(['position']);
    }

    public function testUpdatePosition(): void
    {
        /**
         * @var User
         */
        $user = User::makeFactory()->admin()->create();

        /**
         * @var Collection<Page>|array<Page>
         */
        $pages = Page::makeFactory()->count(3)->active()->withUser()->create();

        $this->assertDatabaseHas('pages', [
            'id' => $pages[0]->id,
            'position' => 0
        ]);

        $this->assertDatabaseHas('pages', [
            'id' => $pages[2]->id,
            'position' => 2
        ]);

        Auth::login($user);

        $this->patch(route('admin.page.update_position', [$pages[2]->id]), [
            'position' => 0
        ]);

        $this->assertDatabaseHas('pages', [
            'id' => $pages[2]->id,
            'position' => 0
        ]);

        $this->assertDatabaseHas('pages', [
            'id' => $pages[0]->id,
            'position' => 1
        ]);
    }
}
