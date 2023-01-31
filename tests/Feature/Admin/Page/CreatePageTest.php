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
use N1ebieski\ICore\Models\PageLang\PageLang;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CreatePageTest extends TestCase
{
    use DatabaseTransactions;

    public function testCreateAsGuest(): void
    {
        $response = $this->get(route('admin.page.create'));

        $response->assertRedirect(route('login'));
    }

    public function testCreateWithoutPermission(): void
    {
        /**
         * @var User
         */
        $user = User::makeFactory()->create();

        Auth::login($user);

        $response = $this->get(route('admin.page.create'));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testCreate(): void
    {
        /**
         * @var User
         */
        $user = User::makeFactory()->admin()->create();

        Auth::login($user);

        $response = $this->get(route('admin.page.create'));

        $response->assertOk()->assertViewIs('icore::admin.page.create');
        $response->assertSee(route('admin.page.store'), false);
    }

    public function testStoreAsGuest(): void
    {
        $response = $this->post(route('admin.page.store'));

        $response->assertRedirect(route('login'));
    }

    public function testStoreWithoutPermission(): void
    {
        /**
         * @var User
         */
        $user = User::makeFactory()->create();

        Auth::login($user);

        $response = $this->post(route('admin.page.store'));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testStoreValidationFail(): void
    {
        /**
         * @var User
         */
        $user = User::makeFactory()->admin()->create();

        Auth::login($user);

        $response = $this->post(route('admin.page.store'), [
            'title' => '',
            'content_html' => 'Ten page został zaktualizowany.',
            'status' => 232
        ]);

        $response->assertSessionHasErrors(['title', 'status']);
    }

    public function testStoreRoot(): void
    {
        /**
         * @var User
         */
        $user = User::makeFactory()->admin()->create();

        Auth::login($user);

        $response = $this->post(route('admin.page.store'), [
            'title' => 'Page dodany.',
            'content_html' => 'Ten page został dodany.',
            'status' => 1,
        ]);

        $response->assertSessionHas('success');

        /** @var PageLang|null */
        $pageLang = PageLang::where('title', 'Page dodany.')->first();

        $this->assertTrue($pageLang?->exists());

        $response->assertRedirect(route('admin.page.index', [
            'filter' => [
                'search' => "id:\"{$pageLang->page->id}\""
            ]
        ]));
    }

    public function testStoreChildren(): void
    {
        /**
         * @var User
         */
        $user = User::makeFactory()->admin()->create();

        /**
         * @var Page
         */
        $parent = Page::makeFactory()->active()->withUser()->create();

        Auth::login($user);

        $response = $this->post(route('admin.page.store'), [
            'title' => 'Page dodany.',
            'content_html' => 'Ten page został dodany.',
            'status' => 1,
            'parent_id' => $parent->id
        ]);

        $response->assertSessionHas('success');

        /** @var PageLang|null */
        $pageLang = PageLang::where('title', 'Page dodany.')->first();

        $this->assertTrue($pageLang?->exists());

        $response->assertRedirect(route('admin.page.index', [
            'filter' => [
                'search' => "id:\"{$pageLang->page->id}\""
            ]
        ]));

        $this->assertDatabaseHas('pages_closure', [
            'descendant' => $pageLang->page->id,
            'ancestor' => $parent->id,
            'depth' => 1
        ]);
    }
}
