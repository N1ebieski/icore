<?php

namespace N1ebieski\ICore\Tests\Feature\Admin;

use Tests\TestCase;
use N1ebieski\ICore\Models\User;
use Illuminate\Support\Facades\Auth;
use N1ebieski\ICore\Models\Page\Page;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PageTest extends TestCase
{
    use DatabaseTransactions;

    public function testPageIndexAsGuest()
    {
        $response = $this->get(route('admin.page.index'));

        $response->assertRedirect(route('login'));
    }

    public function testPageIndexWithoutPermission()
    {
        $user = User::makeFactory()->create();

        Auth::login($user);

        $response = $this->get(route('admin.page.index'));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testPageIndexPaginate()
    {
        $user = User::makeFactory()->admin()->create();

        $page = Page::makeFactory()->count(50)->active()->withUser()->create();

        Auth::login($user);

        $response = $this->get(route('admin.page.index', ['page' => 2, 'orderby' => 'created_at|asc']));

        $response->assertViewIs('icore::admin.page.index');
        $response->assertSee('class="pagination"', false);
        $response->assertSeeInOrder([$page[30]->title, $page[30]->shortContent], false);
    }

    public function testPageEditAsGuest()
    {
        $response = $this->get(route('admin.page.edit', [43]));

        $response->assertRedirect(route('login'));
    }

    public function testPageEditWithoutPermission()
    {
        $user = User::makeFactory()->create();

        $page = Page::makeFactory()->active()->withUser()->create();

        Auth::login($user);

        $response = $this->get(route('admin.page.edit', [$page->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testNoexistPageEdit()
    {
        $user = User::makeFactory()->admin()->create();

        Auth::login($user);

        $response = $this->get(route('admin.page.edit', [2327382]));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testPageEdit()
    {
        $user = User::makeFactory()->admin()->create();

        $page = Page::makeFactory()->active()->withUser()->create();

        Auth::login($user);

        $response = $this->get(route('admin.page.edit', [$page->id]));

        $response->assertOk()->assertJsonStructure(['success', 'view']);
        $this->assertStringContainsString($page->content, $response->getData()->view);
        $this->assertStringContainsString(route('admin.page.update', [$page->id]), $response->getData()->view);
    }

    public function testPageUpdateAsGuest()
    {
        $response = $this->put(route('admin.page.update', [43]));

        $response->assertRedirect(route('login'));
    }

    public function testPageUpdateWithoutPermission()
    {
        $user = User::makeFactory()->create();

        $page = Page::makeFactory()->active()->withUser()->create();

        Auth::login($user);

        $response = $this->put(route('admin.page.update', [$page->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testNoexistPageUpdate()
    {
        $user = User::makeFactory()->admin()->create();

        Auth::login($user);

        $response = $this->put(route('admin.page.update', [2327382]));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testPageUpdateValidationFail()
    {
        $user = User::makeFactory()->admin()->create();

        $page = Page::makeFactory()->active()->withUser()->create();

        Auth::login($user);

        $response = $this->put(route('admin.page.update', [$page->id]), [
            'title' => '',
            'content_html' => 'Ten page został zaktualizowany.'
        ]);

        $response->assertSessionHasErrors(['title']);
    }

    public function testPageUpdate()
    {
        $user = User::makeFactory()->admin()->create();

        $page = Page::makeFactory()->active()->withUser()->create();

        Auth::login($user);

        $response = $this->put(route('admin.page.update', [$page->id]), [
            'title' => 'Page zaktualizowany.',
            'content_html' => 'Ten page został zaktualizowany.'
        ]);

        $response->assertOk()->assertJsonStructure(['success', 'view']);

        $this->assertStringContainsString('Ten page został zaktualizowany.', $response->getData()->view);
        $this->assertDatabaseHas('pages', [
            'content' => 'Ten page został zaktualizowany.',
            'title' => 'Page zaktualizowany.',
        ]);
    }

    public function testPageEditFullAsGuest()
    {
        $response = $this->get(route('admin.page.edit_full', [43]));

        $response->assertRedirect(route('login'));
    }

    public function testPageEditFullWithoutPermission()
    {
        $user = User::makeFactory()->create();

        $page = Page::makeFactory()->active()->withUser()->create();

        Auth::login($user);

        $response = $this->get(route('admin.page.edit_full', [$page->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testNoexistPageEditFull()
    {
        $user = User::makeFactory()->admin()->create();

        Auth::login($user);

        $response = $this->get(route('admin.page.edit_full', [2327382]));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testPageEditFull()
    {
        $user = User::makeFactory()->admin()->create();

        $page = Page::makeFactory()->active()->withUser()->create();

        Auth::login($user);

        $response = $this->get(route('admin.page.edit_full', [$page->id]));

        $response->assertOk()->assertViewIs('icore::admin.page.edit_full');
        $response->assertSeeInOrder([$page->title, $page->content], false);
        $response->assertSee(route('admin.page.update_full', [$page->id]), false);
    }

    public function testPageUpdateFullAsGuest()
    {
        $response = $this->put(route('admin.page.update_full', [43]));

        $response->assertRedirect(route('login'));
    }

    public function testPageUpdateFullWithoutPermission()
    {
        $user = User::makeFactory()->create();

        $page = Page::makeFactory()->active()->withUser()->create();

        Auth::login($user);

        $response = $this->put(route('admin.page.update_full', [$page->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testNoexistPageUpdateFull()
    {
        $user = User::makeFactory()->admin()->create();

        Auth::login($user);

        $response = $this->put(route('admin.page.update_full', [2327382]));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testPageUpdateFullValidationFail()
    {
        $user = User::makeFactory()->admin()->create();

        $page = Page::makeFactory()->active()->withUser()->create();

        Auth::login($user);

        $response = $this->put(route('admin.page.update_full', [$page->id]), [
            'title' => '',
            'content_html' => 'Ten page został zaktualizowany.',
            'status' => 212
        ]);

        $response->assertSessionHasErrors(['title', 'status']);
    }

    public function testRootPageUpdateFull()
    {
        $user = User::makeFactory()->admin()->create();

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

    public function testChildrenPageUpdateFull()
    {
        $user = User::makeFactory()->admin()->create();

        $parent = Page::makeFactory()->active()->withUser()->create();

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

    public function testPageUpdateStatusAsGuest()
    {
        $response = $this->patch(route('admin.page.update_status', [43]));

        $response->assertRedirect(route('login'));
    }

    public function testPageUpdateStatusWithoutPermission()
    {
        $user = User::makeFactory()->create();

        $page = Page::makeFactory()->active()->withUser()->create();

        Auth::login($user);

        $response = $this->patch(route('admin.page.update_status', [$page->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testNoexistPageUpdateStatus()
    {
        $user = User::makeFactory()->admin()->create();

        Auth::login($user);

        $response = $this->patch(route('admin.page.update_status', [2327382]));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testPageUpdateStatusValidationFail()
    {
        $user = User::makeFactory()->admin()->create();

        $page = Page::makeFactory()->active()->withUser()->create();

        Auth::login($user);

        $response = $this->patch(route('admin.page.update_status', [$page->id]), [
            'status' => 323,
        ]);

        $response->assertSessionHasErrors(['status']);
    }

    public function testPageUpdateStatus()
    {
        $user = User::makeFactory()->admin()->create();

        $page = Page::makeFactory()->active()->withUser()->create();

        Auth::login($user);

        $response = $this->patch(route('admin.page.update_status', [$page->id]), [
            'status' => 0,
        ]);

        $response->assertOk()->assertJsonStructure(['success', 'ancestors', 'descendants']);

        $this->assertDatabaseHas('pages', [
            'id' => $page->id,
            'status' => 0,
        ]);
    }

    public function testPageDestroyAsGuest()
    {
        $response = $this->delete(route('admin.page.destroy', [43]));

        $response->assertRedirect(route('login'));
    }

    public function testPageDestroyWithoutPermission()
    {
        $user = User::makeFactory()->create();

        $page = Page::makeFactory()->active()->withUser()->create();

        Auth::login($user);

        $response = $this->delete(route('admin.page.destroy', [$page->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testNoexistPageDestroy()
    {
        $user = User::makeFactory()->admin()->create();

        Auth::login($user);

        $response = $this->delete(route('admin.page.destroy', [2327382]));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testPageDestroy()
    {
        $user = User::makeFactory()->admin()->create();

        $page = Page::makeFactory()->active()->withUser()->create();

        Auth::login($user);

        $this->assertDatabaseHas('pages', [
            'id' => $page->id,
        ]);

        $response = $this->delete(route('admin.page.destroy', [$page->id]), []);

        $response->assertOk()->assertJsonStructure(['success']);

        $this->assertDatabaseMissing('pages', [
            'id' => $page->id,
        ]);
    }

    public function testPageDestroyGlobalAsGuest()
    {
        $response = $this->delete(route('admin.page.destroy_global'), []);

        $response->assertRedirect(route('login'));
    }

    public function testPageDestroyGlobalWithoutPermission()
    {
        $user = User::makeFactory()->create();

        Auth::login($user);

        $response = $this->delete(route('admin.page.destroy_global'), []);

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testPageDestroyGlobalValidationFail()
    {
        $user = User::makeFactory()->admin()->create();

        Auth::login($user);

        $response = $this->delete(route('admin.page.destroy_global', [
            'dasdas',
            'dada'
        ]));

        $response->assertSessionHasErrors(['select']);
    }

    public function testPageDestroyGlobal()
    {
        $user = User::makeFactory()->admin()->create();

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

    public function testPageCreateAsGuest()
    {
        $response = $this->get(route('admin.page.create'));

        $response->assertRedirect(route('login'));
    }

    public function testPageCreateWithoutPermission()
    {
        $user = User::makeFactory()->create();

        Auth::login($user);

        $response = $this->get(route('admin.page.create'));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testPageCreate()
    {
        $user = User::makeFactory()->admin()->create();

        Auth::login($user);

        $response = $this->get(route('admin.page.create'));

        $response->assertOk()->assertViewIs('icore::admin.page.create');
        $response->assertSee(route('admin.page.store'), false);
    }

    public function testPageStoreAsGuest()
    {
        $response = $this->post(route('admin.page.store'));

        $response->assertRedirect(route('login'));
    }

    public function testPageStoreWithoutPermission()
    {
        $user = User::makeFactory()->create();

        Auth::login($user);

        $response = $this->post(route('admin.page.store'));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testPageStoreValidationFail()
    {
        $user = User::makeFactory()->admin()->create();

        Auth::login($user);

        $response = $this->post(route('admin.page.store'), [
            'title' => '',
            'content_html' => 'Ten page został zaktualizowany.',
            'status' => 232
        ]);

        $response->assertSessionHasErrors(['title', 'status']);
    }

    public function testRootPageStore()
    {
        $user = User::makeFactory()->admin()->create();

        Auth::login($user);

        $response = $this->post(route('admin.page.store'), [
            'title' => 'Page dodany.',
            'content_html' => 'Ten page został dodany.',
            'status' => 1,
        ]);

        $response->assertRedirect(route('admin.page.index'));
        $response->assertSessionHas('success');

        $page = Page::where([
            ['content', 'Ten page został dodany.'],
            ['title', 'Page dodany.'],
            ['parent_id', null]
        ])->first();

        $this->assertTrue($page->exists());
    }

    public function testChildrenPageStore()
    {
        $user = User::makeFactory()->admin()->create();

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

        $page = Page::where([
            ['content', 'Ten page został dodany.'],
            ['title', 'Page dodany.'],
            ['parent_id', $parent->id]
        ])->first();

        $this->assertTrue($page->exists());

        $this->assertDatabaseHas('pages_closure', [
            'descendant' => $page->id,
            'ancestor' => $parent->id,
            'depth' => 1
        ]);
    }

    public function testPageEditPositionAsGuest()
    {
        $response = $this->get(route('admin.page.edit_position', [43]));

        $response->assertRedirect(route('login'));
    }

    public function testPageEditPositionWithoutPermission()
    {
        $user = User::makeFactory()->create();

        $page = Page::makeFactory()->active()->withUser()->create();

        Auth::login($user);

        $response = $this->get(route('admin.page.edit_position', [$page->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testNoexistPageEditPosition()
    {
        $user = User::makeFactory()->admin()->create();

        Auth::login($user);

        $response = $this->get(route('admin.page.edit_position', [2327382]));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testPageEditPosition()
    {
        $user = User::makeFactory()->admin()->create();

        $page = Page::makeFactory()->active()->withUser()->create();

        Auth::login($user);

        $response = $this->get(route('admin.page.edit_position', [$page->id]));

        $response->assertOk()->assertJsonStructure(['success', 'view']);
        $this->assertStringContainsString('value="' . $page->position . '"', $response->getData()->view);
        $this->assertStringContainsString(route('admin.page.update_position', [$page->id]), $response->getData()->view);
    }

    public function testPageUpdatePositionAsGuest()
    {
        $response = $this->patch(route('admin.page.update_position', [2323]));

        $response->assertRedirect(route('login'));
    }

    public function testPageUpdatePositionWithoutPermission()
    {
        $user = User::makeFactory()->create();

        $page = Page::makeFactory()->active()->withUser()->create();

        Auth::login($user);

        $response = $this->patch(route('admin.page.update_position', [$page->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testNoexistPageUpdatePosition()
    {
        $user = User::makeFactory()->admin()->create();

        Auth::login($user);

        $response = $this->patch(route('admin.page.update_position', [2327382]));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testPageUpdatePositionValidationFail()
    {
        $user = User::makeFactory()->admin()->create();

        $page = Page::makeFactory()->active()->withUser()->create();

        Auth::login($user);

        $response = $this->patch(route('admin.page.update_position', [$page->id]), [
            'position' => 1232
        ]);

        $response->assertSessionHasErrors(['position']);
    }

    public function testPageUpdatePosition()
    {
        $user = User::makeFactory()->admin()->create();

        $page = Page::makeFactory()->count(3)->active()->withUser()->create();

        $this->assertDatabaseHas('pages', [
            'id' => $page[0]->id,
            'position' => 0
        ]);

        $this->assertDatabaseHas('pages', [
            'id' => $page[2]->id,
            'position' => 2
        ]);

        Auth::login($user);

        $response = $this->patch(route('admin.page.update_position', [$page[2]->id]), [
            'position' => 0
        ]);

        $this->assertDatabaseHas('pages', [
            'id' => $page[2]->id,
            'position' => 0
        ]);

        $this->assertDatabaseHas('pages', [
            'id' => $page[0]->id,
            'position' => 1
        ]);
    }
}
