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
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UpdatePageTest extends TestCase
{
    use DatabaseTransactions;

    public function testEditAsGuest(): void
    {
        $response = $this->get(route('admin.page.edit', [43]));

        $response->assertRedirect(route('login'));
    }

    public function testEditWithoutPermission(): void
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

        $response = $this->get(route('admin.page.edit', [$page->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testEditNoExist(): void
    {
        /**
         * @var User
         */
        $user = User::makeFactory()->admin()->create();

        Auth::login($user);

        $response = $this->get(route('admin.page.edit', [2327382]));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testEdit(): void
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

        $response = $this->get(route('admin.page.edit', [$page->id]));

        $response->assertOk()->assertJsonStructure(['view']);

        /**
         * @var JsonResponse
         */
        $baseResponse = $response->baseResponse;

        $this->assertStringContainsString($page->content ?? '', $baseResponse->getData()->view);
        $this->assertStringContainsString(
            URL::route('admin.page.update', [$page->id]),
            $baseResponse->getData()->view
        );
    }

    public function testUpdateAsGuest(): void
    {
        $response = $this->put(route('admin.page.update', [43]));

        $response->assertRedirect(route('login'));
    }

    public function testUpdateWithoutPermission(): void
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

        $response = $this->put(route('admin.page.update', [$page->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testUpdateNoExist(): void
    {
        /**
         * @var User
         */
        $user = User::makeFactory()->admin()->create();

        Auth::login($user);

        $response = $this->put(route('admin.page.update', [2327382]));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testUpdateValidationFail(): void
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

        $response = $this->put(route('admin.page.update', [$page->id]), [
            'title' => '',
            'content_html' => 'Ten page został zaktualizowany.'
        ]);

        $response->assertSessionHasErrors(['title']);
    }

    public function testUpdate(): void
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

        $response = $this->put(route('admin.page.update', [$page->id]), [
            'title' => 'Page zaktualizowany.',
            'content_html' => 'Ten page został zaktualizowany.'
        ]);

        $response->assertOk()->assertJsonStructure(['view']);

        /**
         * @var JsonResponse
         */
        $baseResponse = $response->baseResponse;

        $this->assertStringContainsString(
            'Ten page został zaktualizowany.',
            $baseResponse->getData()->view
        );

        $this->assertDatabaseHas('pages_langs', [
            'page_id' => $page->id,
            'content' => 'Ten page został zaktualizowany.',
            'title' => 'Page zaktualizowany.',
        ]);
    }

    public function testEditFullAsGuest(): void
    {
        $response = $this->get(route('admin.page.edit_full', [43]));

        $response->assertRedirect(route('login'));
    }

    public function testEditFullWithoutPermission(): void
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

        $response = $this->get(route('admin.page.edit_full', [$page->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testEditFullNoExist(): void
    {
        /**
         * @var User
         */
        $user = User::makeFactory()->admin()->create();

        Auth::login($user);

        $response = $this->get(route('admin.page.edit_full', [2327382]));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testEditFull(): void
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

        $response = $this->get(route('admin.page.edit_full', [$page->id]));

        $response->assertOk()->assertViewIs('icore::admin.page.edit_full');

        $response->assertSeeInOrder([$page->title, $page->content], false);
        $response->assertSee(route('admin.page.update_full', [$page->id]), false);
    }

    public function testUpdateFullAsGuest(): void
    {
        $response = $this->put(route('admin.page.update_full', [43]));

        $response->assertRedirect(route('login'));
    }

    public function testUpdateFullWithoutPermission(): void
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

        $response = $this->put(route('admin.page.update_full', [$page->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testUpdateFullNoExist(): void
    {
        /**
         * @var User
         */
        $user = User::makeFactory()->admin()->create();

        Auth::login($user);

        $response = $this->put(route('admin.page.update_full', [2327382]));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testUpdateFullValidationFail(): void
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

        $response = $this->put(route('admin.page.update_full', [$page->id]), [
            'title' => '',
            'content_html' => 'Ten page został zaktualizowany.',
            'status' => 212
        ]);

        $response->assertSessionHasErrors(['title', 'status']);
    }

    public function testUpdateFullRoot(): void
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

        $response = $this->put(route('admin.page.update_full', [$page->id]), [
            'title' => 'Page zaktualizowany.',
            'content_html' => 'Ten page został zaktualizowany.',
            'user' => $user->id,
            'status' => 1
        ]);

        $response->assertRedirect(route('admin.page.edit_full', [$page->id]));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('pages', [
            'id' => $page->id,
            'parent_id' => null
        ]);

        $this->assertDatabaseHas('pages_langs', [
            'page_id' => $page->id,
            'content' => 'Ten page został zaktualizowany.',
            'title' => 'Page zaktualizowany.'
        ]);
    }

    public function testUpdateFullChildren(): void
    {
        /**
         * @var User
         */
        $user = User::makeFactory()->admin()->create();

        /**
         * @var Page
         */
        $parent = Page::makeFactory()->active()->withUser()->create();

        /**
         * @var Page
         */
        $page = Page::makeFactory()->active()->withUser()->create();

        Auth::login($user);

        $this->get(route('admin.page.edit_full', [$page->id]));

        $response = $this->put(route('admin.page.update_full', [$page->id]), [
            'title' => 'Page zaktualizowany.',
            'content_html' => 'Ten page został zaktualizowany.',
            'status' => 1,
            'user' => $user->id,
            'parent_id' => $parent->id
        ]);

        $response->assertRedirect(route('admin.page.edit_full', [$page->id]));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('pages', [
            'id' => $page->id,
            'parent_id' => $parent->id
        ]);

        $this->assertDatabaseHas('pages_langs', [
            'page_id' => $page->id,
            'content' => 'Ten page został zaktualizowany.',
            'title' => 'Page zaktualizowany.'
        ]);

        $this->assertDatabaseHas('pages_closure', [
            'descendant' => $page->id,
            'ancestor' => $parent->id,
            'depth' => 1
        ]);
    }
}
