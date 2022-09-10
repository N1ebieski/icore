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

namespace N1ebieski\ICore\Tests\Feature\Admin;

use Tests\TestCase;
use N1ebieski\ICore\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Auth;
use N1ebieski\ICore\Models\Page\Page;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PageTest extends TestCase
{
    use DatabaseTransactions;

    public function testPageIndexAsGuest(): void
    {
        $response = $this->get(route('admin.page.index'));

        $response->assertRedirect(route('login'));
    }

    public function testPageIndexWithoutPermission(): void
    {
        /**
         * @var User
         */
        $user = User::makeFactory()->create();

        Auth::login($user);

        $response = $this->get(route('admin.page.index'));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testPageIndexPaginate(): void
    {
        /**
         * @var User
         */
        $user = User::makeFactory()->admin()->create();

        /**
         * @var Collection<Page>|array<Page>
         */
        $pages = Page::makeFactory()->count(50)->active()->withUser()->create();

        Auth::login($user);

        $response = $this->get(route('admin.page.index', ['page' => 2, 'orderby' => 'created_at|asc']));

        $response->assertViewIs('icore::admin.page.index');
        $response->assertSee('class="pagination"', false);
        $response->assertSeeInOrder([$pages[30]->title, $pages[30]->shortContent], false);
    }

    public function testPageEditAsGuest(): void
    {
        $response = $this->get(route('admin.page.edit', [43]));

        $response->assertRedirect(route('login'));
    }

    public function testPageEditWithoutPermission(): void
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

    public function testNoexistPageEdit(): void
    {
        /**
         * @var User
         */
        $user = User::makeFactory()->admin()->create();

        Auth::login($user);

        $response = $this->get(route('admin.page.edit', [2327382]));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testPageEdit(): void
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

    public function testPageUpdateAsGuest(): void
    {
        $response = $this->put(route('admin.page.update', [43]));

        $response->assertRedirect(route('login'));
    }

    public function testPageUpdateWithoutPermission(): void
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

    public function testNoexistPageUpdate(): void
    {
        /**
         * @var User
         */
        $user = User::makeFactory()->admin()->create();

        Auth::login($user);

        $response = $this->put(route('admin.page.update', [2327382]));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testPageUpdateValidationFail(): void
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

    public function testPageUpdate(): void
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

        $this->assertDatabaseHas('pages', [
            'content' => 'Ten page został zaktualizowany.',
            'title' => 'Page zaktualizowany.',
        ]);
    }

    public function testPageEditFullAsGuest(): void
    {
        $response = $this->get(route('admin.page.edit_full', [43]));

        $response->assertRedirect(route('login'));
    }

    public function testPageEditFullWithoutPermission(): void
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

    public function testNoexistPageEditFull(): void
    {
        /**
         * @var User
         */
        $user = User::makeFactory()->admin()->create();

        Auth::login($user);

        $response = $this->get(route('admin.page.edit_full', [2327382]));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testPageEditFull(): void
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

    public function testPageUpdateFullAsGuest(): void
    {
        $response = $this->put(route('admin.page.update_full', [43]));

        $response->assertRedirect(route('login'));
    }

    public function testPageUpdateFullWithoutPermission(): void
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

    public function testNoexistPageUpdateFull(): void
    {
        /**
         * @var User
         */
        $user = User::makeFactory()->admin()->create();

        Auth::login($user);

        $response = $this->put(route('admin.page.update_full', [2327382]));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testPageUpdateFullValidationFail(): void
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

    public function testRootPageUpdateFull(): void
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
            'content' => 'Ten page został zaktualizowany.',
            'title' => 'Page zaktualizowany.',
            'parent_id' => null
        ]);
    }

    public function testChildrenPageUpdateFull(): void
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
            'content' => 'Ten page został zaktualizowany.',
            'title' => 'Page zaktualizowany.',
            'parent_id' => $parent->id
        ]);

        $this->assertDatabaseHas('pages_closure', [
            'descendant' => $page->id,
            'ancestor' => $parent->id,
            'depth' => 1
        ]);
    }

    public function testPageUpdateStatusAsGuest(): void
    {
        $response = $this->patch(route('admin.page.update_status', [43]));

        $response->assertRedirect(route('login'));
    }

    public function testPageUpdateStatusWithoutPermission(): void
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

    public function testNoexistPageUpdateStatus(): void
    {
        /**
         * @var User
         */
        $user = User::makeFactory()->admin()->create();

        Auth::login($user);

        $response = $this->patch(route('admin.page.update_status', [2327382]));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testPageUpdateStatusValidationFail(): void
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

    public function testPageUpdateStatus(): void
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

    public function testPageDestroyAsGuest(): void
    {
        $response = $this->delete(route('admin.page.destroy', [43]));

        $response->assertRedirect(route('login'));
    }

    public function testPageDestroyWithoutPermission(): void
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

        $response = $this->delete(route('admin.page.destroy', [$page->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testNoexistPageDestroy(): void
    {
        /**
         * @var User
         */
        $user = User::makeFactory()->admin()->create();

        Auth::login($user);

        $response = $this->delete(route('admin.page.destroy', [2327382]));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testPageDestroy(): void
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

        $this->assertDatabaseHas('pages', [
            'id' => $page->id,
        ]);

        $response = $this->delete(route('admin.page.destroy', [$page->id]), []);

        $response->assertOk();

        $this->assertDatabaseMissing('pages', [
            'id' => $page->id,
        ]);
    }

    public function testPageDestroyGlobalAsGuest(): void
    {
        $response = $this->delete(route('admin.page.destroy_global'), []);

        $response->assertRedirect(route('login'));
    }

    public function testPageDestroyGlobalWithoutPermission(): void
    {
        /**
         * @var User
         */
        $user = User::makeFactory()->create();

        Auth::login($user);

        $response = $this->delete(route('admin.page.destroy_global'), []);

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testPageDestroyGlobalValidationFail(): void
    {
        /**
         * @var User
         */
        $user = User::makeFactory()->admin()->create();

        Auth::login($user);

        $response = $this->delete(route('admin.page.destroy_global', [
            'dasdas',
            'dada'
        ]));

        $response->assertSessionHasErrors(['select']);
    }

    public function testPageDestroyGlobal(): void
    {
        /**
         * @var User
         */
        $user = User::makeFactory()->admin()->create();

        /**
         * @var Page
         */
        $page = Page::makeFactory()->count(10)->active()->withUser()->create();

        Auth::login($user);

        $this->get(route('admin.page.index'));

        $select = collect($page)->pluck('id')->take(5)->toArray();

        $response = $this->delete(route('admin.page.destroy_global'), [
            'select' => $select,
        ]);

        $response->assertRedirect(route('admin.page.index'));
        $response->assertSessionHas('success');

        $deleted = Page::whereIn('id', $select)->get();

        $this->assertTrue($deleted->count() === 0);
    }

    public function testPageCreateAsGuest(): void
    {
        $response = $this->get(route('admin.page.create'));

        $response->assertRedirect(route('login'));
    }

    public function testPageCreateWithoutPermission(): void
    {
        /**
         * @var User
         */
        $user = User::makeFactory()->create();

        Auth::login($user);

        $response = $this->get(route('admin.page.create'));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testPageCreate(): void
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

    public function testPageStoreAsGuest(): void
    {
        $response = $this->post(route('admin.page.store'));

        $response->assertRedirect(route('login'));
    }

    public function testPageStoreWithoutPermission(): void
    {
        /**
         * @var User
         */
        $user = User::makeFactory()->create();

        Auth::login($user);

        $response = $this->post(route('admin.page.store'));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testPageStoreValidationFail(): void
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

    public function testRootPageStore(): void
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

        $response->assertRedirect(route('admin.page.index'));
        $response->assertSessionHas('success');

        /** @var Page|null */
        $page = Page::where([
            ['content', 'Ten page został dodany.'],
            ['title', 'Page dodany.'],
            ['parent_id', null]
        ])->first();

        $this->assertTrue(!is_null($page) && $page->exists());
    }

    public function testChildrenPageStore(): void
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

        $response->assertRedirect(route('admin.page.index'));
        $response->assertSessionHas('success');

        /** @var Page|null */
        $page = Page::where([
            ['content', 'Ten page został dodany.'],
            ['title', 'Page dodany.'],
            ['parent_id', $parent->id]
        ])->first();

        $this->assertTrue(!is_null($page) && $page->exists());

        $this->assertDatabaseHas('pages_closure', [
            'descendant' => $page?->id,
            'ancestor' => $parent->id,
            'depth' => 1
        ]);
    }

    public function testPageEditPositionAsGuest(): void
    {
        $response = $this->get(route('admin.page.edit_position', [43]));

        $response->assertRedirect(route('login'));
    }

    public function testPageEditPositionWithoutPermission(): void
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

    public function testNoexistPageEditPosition(): void
    {
        /**
         * @var User
         */
        $user = User::makeFactory()->admin()->create();

        Auth::login($user);

        $response = $this->get(route('admin.page.edit_position', [2327382]));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testPageEditPosition(): void
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

    public function testPageUpdatePositionAsGuest(): void
    {
        $response = $this->patch(route('admin.page.update_position', [2323]));

        $response->assertRedirect(route('login'));
    }

    public function testPageUpdatePositionWithoutPermission(): void
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

    public function testNoexistPageUpdatePosition(): void
    {
        /**
         * @var User
         */
        $user = User::makeFactory()->admin()->create();

        Auth::login($user);

        $response = $this->patch(route('admin.page.update_position', [2327382]));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testPageUpdatePositionValidationFail(): void
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

    public function testPageUpdatePosition(): void
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
