<?php

namespace N1ebieski\ICore\Tests\Feature\Admin;

use Tests\TestCase;
use N1ebieski\ICore\Models\User;
use Illuminate\Support\Facades\Auth;
use N1ebieski\ICore\Models\Page\Page;
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
        $user = User::factory()->create();

        Auth::login($user);

        $response = $this->get(route('admin.page.index'));

        $response->assertStatus(403);
    }

    public function testPageIndexPaginate()
    {
        $user = User::factory()->admin()->create();

        $page = Page::factory()->count(50)->active()->withUser()->create();

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
        $user = User::factory()->create();

        $page = Page::factory()->active()->withUser()->create();

        Auth::login($user);

        $response = $this->get(route('admin.page.edit', [$page->id]));

        $response->assertStatus(403);
    }

    public function testNoexistPageEdit()
    {
        $user = User::factory()->admin()->create();

        Auth::login($user);

        $response = $this->get(route('admin.page.edit', [2327382]));

        $response->assertStatus(404);
    }

    public function testPageEdit()
    {
        $user = User::factory()->admin()->create();

        $page = Page::factory()->active()->withUser()->create();

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
        $user = User::factory()->create();

        $page = Page::factory()->active()->withUser()->create();

        Auth::login($user);

        $response = $this->put(route('admin.page.update', [$page->id]));

        $response->assertStatus(403);
    }

    public function testNoexistPageUpdate()
    {
        $user = User::factory()->admin()->create();

        Auth::login($user);

        $response = $this->put(route('admin.page.update', [2327382]));

        $response->assertStatus(404);
    }

    public function testPageUpdateValidationFail()
    {
        $user = User::factory()->admin()->create();

        $page = Page::factory()->active()->withUser()->create();

        Auth::login($user);

        $response = $this->put(route('admin.page.update', [$page->id]), [
            'title' => '',
            'content_html' => 'Ten page został zaktualizowany.'
        ]);

        $response->assertSessionHasErrors(['title']);

        $this->assertTrue(Auth::check());
    }

    public function testPageUpdate()
    {
        $user = User::factory()->admin()->create();

        $page = Page::factory()->active()->withUser()->create();

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
        $this->assertTrue(Auth::check());
    }

    public function testPageEditFullAsGuest()
    {
        $response = $this->get(route('admin.page.edit_full', [43]));

        $response->assertRedirect(route('login'));
    }

    public function testPageEditFullWithoutPermission()
    {
        $user = User::factory()->create();

        $page = Page::factory()->active()->withUser()->create();

        Auth::login($user);

        $response = $this->get(route('admin.page.edit_full', [$page->id]));

        $response->assertStatus(403);
    }

    public function testNoexistPageEditFull()
    {
        $user = User::factory()->admin()->create();

        Auth::login($user);

        $response = $this->get(route('admin.page.edit_full', [2327382]));

        $response->assertStatus(404);
    }

    public function testPageEditFull()
    {
        $user = User::factory()->admin()->create();

        $page = Page::factory()->active()->withUser()->create();

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
        $user = User::factory()->create();

        $page = Page::factory()->active()->withUser()->create();

        Auth::login($user);

        $response = $this->put(route('admin.page.update_full', [$page->id]));

        $response->assertStatus(403);
    }

    public function testNoexistPageUpdateFull()
    {
        $user = User::factory()->admin()->create();

        Auth::login($user);

        $response = $this->put(route('admin.page.update_full', [2327382]));

        $response->assertStatus(404);
    }

    public function testPageUpdateFullValidationFail()
    {
        $user = User::factory()->admin()->create();

        $page = Page::factory()->active()->withUser()->create();

        Auth::login($user);

        $response = $this->put(route('admin.page.update_full', [$page->id]), [
            'title' => '',
            'content_html' => 'Ten page został zaktualizowany.',
            'status' => 212
        ]);

        $response->assertSessionHasErrors(['title', 'status']);

        $this->assertTrue(Auth::check());
    }

    public function testRootPageUpdateFull()
    {
        $user = User::factory()->admin()->create();

        $page = Page::factory()->active()->withUser()->create();

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

        $this->assertTrue(Auth::check());
    }

    public function testChildrenPageUpdateFull()
    {
        $user = User::factory()->admin()->create();

        $parent = Page::factory()->active()->withUser()->create();

        $page = Page::factory()->active()->withUser()->create();

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

        $this->assertTrue(Auth::check());
    }

    public function testPageUpdateStatusAsGuest()
    {
        $response = $this->patch(route('admin.page.update_status', [43]));

        $response->assertRedirect(route('login'));
    }

    public function testPageUpdateStatusWithoutPermission()
    {
        $user = User::factory()->create();

        $page = Page::factory()->active()->withUser()->create();

        Auth::login($user);

        $response = $this->patch(route('admin.page.update_status', [$page->id]));

        $response->assertStatus(403);
    }

    public function testNoexistPageUpdateStatus()
    {
        $user = User::factory()->admin()->create();

        Auth::login($user);

        $response = $this->patch(route('admin.page.update_status', [2327382]));

        $response->assertStatus(404);
    }

    public function testPageUpdateStatusValidationFail()
    {
        $user = User::factory()->admin()->create();

        $page = Page::factory()->active()->withUser()->create();

        Auth::login($user);

        $response = $this->patch(route('admin.page.update_status', [$page->id]), [
            'status' => 323,
        ]);

        $response->assertSessionHasErrors(['status']);

        $this->assertTrue(Auth::check());
    }

    public function testPageUpdateStatus()
    {
        $user = User::factory()->admin()->create();

        $page = Page::factory()->active()->withUser()->create();

        Auth::login($user);

        $response = $this->patch(route('admin.page.update_status', [$page->id]), [
            'status' => 0,
        ]);

        $response->assertOk()->assertJsonStructure(['success', 'ancestors', 'descendants']);

        $this->assertDatabaseHas('pages', [
            'id' => $page->id,
            'status' => 0,
        ]);

        $this->assertTrue(Auth::check());
    }

    public function testPageDestroyAsGuest()
    {
        $response = $this->delete(route('admin.page.destroy', [43]));

        $response->assertRedirect(route('login'));
    }

    public function testPageDestroyWithoutPermission()
    {
        $user = User::factory()->create();

        $page = Page::factory()->active()->withUser()->create();

        Auth::login($user);

        $response = $this->delete(route('admin.page.destroy', [$page->id]));

        $response->assertStatus(403);
    }

    public function testNoexistPageDestroy()
    {
        $user = User::factory()->admin()->create();

        Auth::login($user);

        $response = $this->delete(route('admin.page.destroy', [2327382]));

        $response->assertStatus(404);
    }

    public function testPageDestroy()
    {
        $user = User::factory()->admin()->create();

        $page = Page::factory()->active()->withUser()->create();

        Auth::login($user);

        $this->assertDatabaseHas('pages', [
            'id' => $page->id,
        ]);

        $response = $this->delete(route('admin.page.destroy', [$page->id]), []);

        $response->assertOk()->assertJsonStructure(['success']);

        $this->assertDatabaseMissing('pages', [
            'id' => $page->id,
        ]);

        $this->assertTrue(Auth::check());
    }

    public function testPageDestroyGlobalAsGuest()
    {
        $response = $this->delete(route('admin.page.destroy_global'), []);

        $response->assertRedirect(route('login'));
    }

    public function testPageDestroyGlobalWithoutPermission()
    {
        $user = User::factory()->create();

        Auth::login($user);

        $response = $this->delete(route('admin.page.destroy_global'), []);

        $response->assertStatus(403);
    }

    public function testPageDestroyGlobalValidationFail()
    {
        $user = User::factory()->admin()->create();

        Auth::login($user);

        $response = $this->delete(route('admin.page.destroy_global', [
            'dasdas',
            'dada'
        ]));

        $response->assertSessionHasErrors(['select']);
    }

    public function testPageDestroyGlobal()
    {
        $user = User::factory()->admin()->create();

        $page = Page::factory()->count(10)->active()->withUser()->create();

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

        $this->assertTrue(Auth::check());
    }

    public function testPageCreateAsGuest()
    {
        $response = $this->get(route('admin.page.create'));

        $response->assertRedirect(route('login'));
    }

    public function testPageCreateWithoutPermission()
    {
        $user = User::factory()->create();

        Auth::login($user);

        $response = $this->get(route('admin.page.create'));

        $response->assertStatus(403);
    }

    public function testPageCreate()
    {
        $user = User::factory()->admin()->create();

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
        $user = User::factory()->create();

        Auth::login($user);

        $response = $this->post(route('admin.page.store'));

        $response->assertStatus(403);
    }

    public function testPageStoreValidationFail()
    {
        $user = User::factory()->admin()->create();

        Auth::login($user);

        $response = $this->post(route('admin.page.store'), [
            'title' => '',
            'content_html' => 'Ten page został zaktualizowany.',
            'status' => 232
        ]);

        $response->assertSessionHasErrors(['title', 'status']);

        $this->assertTrue(Auth::check());
    }

    public function testRootPageStore()
    {
        $user = User::factory()->admin()->create();

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

        $this->assertTrue(Auth::check());
    }

    public function testChildrenPageStore()
    {
        $user = User::factory()->admin()->create();

        $parent = Page::factory()->active()->withUser()->create();

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

        $this->assertTrue(Auth::check());
    }

    public function testPageEditPositionAsGuest()
    {
        $response = $this->get(route('admin.page.edit_position', [43]));

        $response->assertRedirect(route('login'));
    }

    public function testPageEditPositionWithoutPermission()
    {
        $user = User::factory()->create();

        $page = Page::factory()->active()->withUser()->create();

        Auth::login($user);

        $response = $this->get(route('admin.page.edit_position', [$page->id]));

        $response->assertStatus(403);
    }

    public function testNoexistPageEditPosition()
    {
        $user = User::factory()->admin()->create();

        Auth::login($user);

        $response = $this->get(route('admin.page.edit_position', [2327382]));

        $response->assertStatus(404);
    }

    public function testPageEditPosition()
    {
        $user = User::factory()->admin()->create();

        $page = Page::factory()->active()->withUser()->create();

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
        $user = User::factory()->create();

        $page = Page::factory()->active()->withUser()->create();

        Auth::login($user);

        $response = $this->patch(route('admin.page.update_position', [$page->id]));

        $response->assertStatus(403);
    }

    public function testNoexistPageUpdatePosition()
    {
        $user = User::factory()->admin()->create();

        Auth::login($user);

        $response = $this->patch(route('admin.page.update_position', [2327382]));

        $response->assertStatus(404);
    }

    public function testPageUpdatePositionValidationFail()
    {
        $user = User::factory()->admin()->create();

        $page = Page::factory()->active()->withUser()->create();

        Auth::login($user);

        $response = $this->patch(route('admin.page.update_position', [$page->id]), [
            'position' => 1232
        ]);

        $response->assertSessionHasErrors(['position']);

        $this->assertTrue(Auth::check());
    }

    public function testPageUpdatePosition()
    {
        $user = User::factory()->admin()->create();

        $page = Page::factory()->count(3)->active()->withUser()->create();

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

        $this->assertTrue(Auth::check());
    }
}
