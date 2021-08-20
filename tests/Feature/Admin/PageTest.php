<?php

namespace N1ebieski\ICore\Tests\Feature\Admin;

use Tests\TestCase;
use N1ebieski\ICore\Models\User;
use N1ebieski\ICore\Models\Page\Page;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Auth;

class PageTest extends TestCase
{
    use DatabaseTransactions;

    public function test_page_index_as_guest()
    {
        $response = $this->get(route('admin.page.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_page_index_without_permission()
    {
        $user = factory(User::class)->create();

        Auth::login($user, true);

        $response = $this->get(route('admin.page.index'));

        $response->assertStatus(403);
    }

    public function test_page_index_paginate()
    {
        $user = factory(User::class)->states('admin')->create();

        $page = factory(Page::class, 50)->states(['active', 'with_user'])->create();

        Auth::login($user, true);

        $response = $this->get(route('admin.page.index', ['page' => 2, 'orderby' => 'created_at|asc']));

        $response->assertViewIs('icore::admin.page.index');
        $response->assertSee('class="pagination"');
        $response->assertSeeInOrder([$page[30]->title, $page[30]->shortContent]);
    }

    public function test_page_edit_as_guest()
    {
        $response = $this->get(route('admin.page.edit', [43]));

        $response->assertRedirect(route('login'));
    }

    public function test_page_edit_without_permission()
    {
        $user = factory(User::class)->create();

        $page = factory(Page::class)->states(['active', 'with_user'])->create();

        Auth::login($user, true);

        $response = $this->get(route('admin.page.edit', [$page->id]));

        $response->assertStatus(403);
    }

    public function test_noexist_page_edit()
    {
        $user = factory(User::class)->states('admin')->create();

        Auth::login($user, true);

        $response = $this->get(route('admin.page.edit', [2327382]));

        $response->assertStatus(404);
    }

    public function test_page_edit()
    {
        $user = factory(User::class)->states('admin')->create();

        $page = factory(Page::class)->states(['active', 'with_user'])->create();

        Auth::login($user, true);

        $response = $this->get(route('admin.page.edit', [$page->id]));

        $response->assertOk()->assertJsonStructure(['success', 'view']);
        $this->assertStringContainsString($page->content, $response->getData()->view);
        $this->assertStringContainsString(route('admin.page.update', [$page->id]), $response->getData()->view);

    }

    public function test_page_update_as_guest()
    {
        $response = $this->put(route('admin.page.update', [43]));

        $response->assertRedirect(route('login'));
    }

    public function test_page_update_without_permission()
    {
        $user = factory(User::class)->create();

        $page = factory(Page::class)->states(['active', 'with_user'])->create();

        Auth::login($user, true);

        $response = $this->put(route('admin.page.update', [$page->id]));

        $response->assertStatus(403);
    }

    public function test_noexist_page_update()
    {
        $user = factory(User::class)->states('admin')->create();

        Auth::login($user, true);

        $response = $this->put(route('admin.page.update', [2327382]));

        $response->assertStatus(404);
    }

    public function test_page_update_validation_fail()
    {
        $user = factory(User::class)->states('admin')->create();

        $page = factory(Page::class)->states(['active', 'with_user'])->create();

        Auth::login($user, true);

        $response = $this->put(route('admin.page.update', [$page->id]), [
            'title' => '',
            'content_html' => 'Ten page został zaktualizowany.'
        ]);

        $response->assertSessionHasErrors(['title']);

        $this->assertTrue(Auth::check());
    }

    public function test_page_update()
    {
        $user = factory(User::class)->states('admin')->create();

        $page = factory(Page::class)->states(['active', 'with_user'])->create();

        Auth::login($user, true);

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

    public function test_page_editFull_as_guest()
    {
        $response = $this->get(route('admin.page.edit_full', [43]));

        $response->assertRedirect(route('login'));
    }

    public function test_page_editFull_without_permission()
    {
        $user = factory(User::class)->create();

        $page = factory(Page::class)->states(['active', 'with_user'])->create();

        Auth::login($user, true);

        $response = $this->get(route('admin.page.edit_full', [$page->id]));

        $response->assertStatus(403);
    }

    public function test_noexist_page_editFull()
    {
        $user = factory(User::class)->states('admin')->create();

        Auth::login($user, true);

        $response = $this->get(route('admin.page.edit_full', [2327382]));

        $response->assertStatus(404);
    }

    public function test_page_editFull()
    {
        $user = factory(User::class)->states('admin')->create();

        $page = factory(Page::class)->states(['active', 'with_user'])->create();

        Auth::login($user, true);

        $response = $this->get(route('admin.page.edit_full', [$page->id]));

        $response->assertOk()->assertViewIs('icore::admin.page.edit_full');
        $response->assertSeeInOrder([$page->title, $page->content]);
        $response->assertSee(route('admin.page.update_full', [$page->id]));

    }

    public function test_page_updateFull_as_guest()
    {
        $response = $this->put(route('admin.page.update_full', [43]));

        $response->assertRedirect(route('login'));
    }

    public function test_page_updateFull_without_permission()
    {
        $user = factory(User::class)->create();

        $page = factory(Page::class)->states(['active', 'with_user'])->create();

        Auth::login($user, true);

        $response = $this->put(route('admin.page.update_full', [$page->id]));

        $response->assertStatus(403);
    }

    public function test_noexist_page_updateFull()
    {
        $user = factory(User::class)->states('admin')->create();

        Auth::login($user, true);

        $response = $this->put(route('admin.page.update_full', [2327382]));

        $response->assertStatus(404);
    }

    public function test_page_updateFull_validation_fail()
    {
        $user = factory(User::class)->states('admin')->create();

        $page = factory(Page::class)->states(['active', 'with_user'])->create();

        Auth::login($user, true);

        $response = $this->put(route('admin.page.update_full', [$page->id]), [
            'title' => '',
            'content_html' => 'Ten page został zaktualizowany.',
            'status' => 212
        ]);

        $response->assertSessionHasErrors(['title', 'status']);

        $this->assertTrue(Auth::check());
    }

    public function test_root_page_update_full()
    {
        $user = factory(User::class)->states('admin')->create();

        $page = factory(Page::class)->states(['active', 'with_user'])->create();

        Auth::login($user, true);

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

    public function test_children_page_update_full()
    {
        $user = factory(User::class)->states('admin')->create();

        $parent = factory(Page::class)->states(['active', 'with_user'])->create();

        $page = factory(Page::class)->states(['active', 'with_user'])->create();

        Auth::login($user, true);

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

    public function test_page_updateStatus_as_guest()
    {
        $response = $this->patch(route('admin.page.update_status', [43]));

        $response->assertRedirect(route('login'));
    }

    public function test_page_updateStatus_without_permission()
    {
        $user = factory(User::class)->create();

        $page = factory(Page::class)->states(['active', 'with_user'])->create();

        Auth::login($user, true);

        $response = $this->patch(route('admin.page.update_status', [$page->id]));

        $response->assertStatus(403);
    }

    public function test_noexist_page_updateStatus()
    {
        $user = factory(User::class)->states('admin')->create();

        Auth::login($user, true);

        $response = $this->patch(route('admin.page.update_status', [2327382]));

        $response->assertStatus(404);
    }

    public function test_page_updateStatus_validation_fail()
    {
        $user = factory(User::class)->states('admin')->create();

        $page = factory(Page::class)->states(['active', 'with_user'])->create();

        Auth::login($user, true);

        $response = $this->patch(route('admin.page.update_status', [$page->id]), [
            'status' => 323,
        ]);

        $response->assertSessionHasErrors(['status']);

        $this->assertTrue(Auth::check());
    }

    public function test_page_updateStatus()
    {
        $user = factory(User::class)->states('admin')->create();

        $page = factory(Page::class)->states(['active', 'with_user'])->create();

        Auth::login($user, true);

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

    public function test_page_destroy_as_guest()
    {
        $response = $this->delete(route('admin.page.destroy', [43]));

        $response->assertRedirect(route('login'));
    }

    public function test_page_destroy_without_permission()
    {
        $user = factory(User::class)->create();

        $page = factory(Page::class)->states(['active', 'with_user'])->create();

        Auth::login($user, true);

        $response = $this->delete(route('admin.page.destroy', [$page->id]));

        $response->assertStatus(403);
    }

    public function test_noexist_page_destroy()
    {
        $user = factory(User::class)->states('admin')->create();

        Auth::login($user, true);

        $response = $this->delete(route('admin.page.destroy', [2327382]));

        $response->assertStatus(404);
    }

    public function test_page_destroy()
    {
        $user = factory(User::class)->states('admin')->create();

        $page = factory(Page::class)->states(['active', 'with_user'])->create();

        Auth::login($user, true);

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

    public function test_page_destroyGlobal_as_guest()
    {
        $response = $this->delete(route('admin.page.destroy_global'), []);

        $response->assertRedirect(route('login'));
    }

    public function test_page_destroyGlobal_without_permission()
    {
        $user = factory(User::class)->create();

        Auth::login($user, true);

        $response = $this->delete(route('admin.page.destroy_global'), []);

        $response->assertStatus(403);
    }

    public function test_page_destroyGlobal_validation_fail()
    {
        $user = factory(User::class)->states('admin')->create();

        Auth::login($user, true);

        $response = $this->delete(route('admin.page.destroy_global', [
            'dasdas',
            'dada'
        ]));

        $response->assertSessionHasErrors(['select']);
    }

    public function test_page_destroyGlobal()
    {
        $user = factory(User::class)->states('admin')->create();

        $page = factory(Page::class, 10)->states(['active', 'with_user'])->create();

        Auth::login($user, true);

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

    public function test_page_create_as_guest()
    {
        $response = $this->get(route('admin.page.create'));

        $response->assertRedirect(route('login'));
    }

    public function test_page_create_without_permission()
    {
        $user = factory(User::class)->create();

        Auth::login($user, true);

        $response = $this->get(route('admin.page.create'));

        $response->assertStatus(403);
    }

    public function test_page_create()
    {
        $user = factory(User::class)->states('admin')->create();

        Auth::login($user, true);

        $response = $this->get(route('admin.page.create'));

        $response->assertOk()->assertViewIs('icore::admin.page.create');
        $response->assertSee(route('admin.page.store'));

    }

    public function test_page_store_as_guest()
    {
        $response = $this->post(route('admin.page.store'));

        $response->assertRedirect(route('login'));
    }

    public function test_page_store_without_permission()
    {
        $user = factory(User::class)->create();

        Auth::login($user, true);

        $response = $this->post(route('admin.page.store'));

        $response->assertStatus(403);
    }

    public function test_page_store_validation_fail()
    {
        $user = factory(User::class)->states('admin')->create();

        Auth::login($user, true);

        $response = $this->post(route('admin.page.store'), [
            'title' => '',
            'content_html' => 'Ten page został zaktualizowany.',
            'status' => 232
        ]);

        $response->assertSessionHasErrors(['title', 'status']);

        $this->assertTrue(Auth::check());
    }

    public function test_root_page_store()
    {
        $user = factory(User::class)->states('admin')->create();

        Auth::login($user, true);

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

    public function test_children_page_store()
    {
        $user = factory(User::class)->states('admin')->create();

        $parent = factory(Page::class)->states(['active', 'with_user'])->create();

        Auth::login($user, true);

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

    public function test_page_edit_position_as_guest()
    {
        $response = $this->get(route('admin.page.edit_position', [43]));

        $response->assertRedirect(route('login'));
    }

    public function test_page_edit_position_without_permission()
    {
        $user = factory(User::class)->create();

        $page = factory(Page::class)->states(['active', 'with_user'])->create();

        Auth::login($user, true);

        $response = $this->get(route('admin.page.edit_position', [$page->id]));

        $response->assertStatus(403);
    }

    public function test_noexist_page_edit_position()
    {
        $user = factory(User::class)->states('admin')->create();

        Auth::login($user, true);

        $response = $this->get(route('admin.page.edit_position', [2327382]));

        $response->assertStatus(404);
    }

    public function test_page_edit_position()
    {
        $user = factory(User::class)->states('admin')->create();

        $page = factory(Page::class)->states(['active', 'with_user'])->create();

        Auth::login($user, true);

        $response = $this->get(route('admin.page.edit_position', [$page->id]));

        $response->assertOk()->assertJsonStructure(['success', 'view']);
        $this->assertStringContainsString('value="'.$page->position.'"', $response->getData()->view);
        $this->assertStringContainsString(route('admin.page.update_position', [$page->id]), $response->getData()->view);

    }

    public function test_page_update_position_as_guest()
    {
        $response = $this->patch(route('admin.page.update_position', [2323]));

        $response->assertRedirect(route('login'));
    }

    public function test_page_update_position_without_permission()
    {
        $user = factory(User::class)->create();

        $page = factory(Page::class)->states(['active', 'with_user'])->create();

        Auth::login($user, true);

        $response = $this->patch(route('admin.page.update_position', [$page->id]));

        $response->assertStatus(403);
    }

    public function test_noexist_page_update_position()
    {
        $user = factory(User::class)->states('admin')->create();

        Auth::login($user, true);

        $response = $this->patch(route('admin.page.update_position', [2327382]));

        $response->assertStatus(404);
    }

    public function test_page_update_position_validation_fail()
    {
        $user = factory(User::class)->states('admin')->create();

        $page = factory(Page::class)->states(['active', 'with_user'])->create();

        Auth::login($user, true);

        $response = $this->patch(route('admin.page.update_position', [$page->id]), [
            'position' => 1232
        ]);

        $response->assertSessionHasErrors(['position']);

        $this->assertTrue(Auth::check());
    }

    public function test_page_update_position()
    {
        $user = factory(User::class)->states('admin')->create();

        $page = factory(Page::class, 3)->states(['active', 'with_user'])->create();

        $this->assertDatabaseHas('pages', [
            'id' => $page[0]->id,
            'position' => 0
        ]);

        $this->assertDatabaseHas('pages', [
            'id' => $page[2]->id,
            'position' => 2
        ]);

        Auth::login($user, true);

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
