<?php

namespace N1ebieski\ICore\Tests\Feature\Admin;

use Tests\TestCase;
use N1ebieski\ICore\Models\User;
use N1ebieski\ICore\Models\Category\Post\Category;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CategoryTest extends TestCase
{
    use DatabaseTransactions;

    public function test_category_search_as_guest()
    {
        $response = $this->get(route('admin.category.post.search'));

        $response->assertRedirect(route('login'));
    }

    public function test_category_search_without_permission()
    {
        $user = factory(User::class)->create();

        Auth::login($user, true);

        $response = $this->get(route('admin.category.post.search'));

        $response->assertStatus(403);
    }

    public function test_category_search_validation_fail()
    {
        $user = factory(User::class)->states('admin')->create();

        Auth::login($user, true);

        $response = $this->get(route('admin.category.post.search', [
            'name' => 'B'
        ]));

        $response->assertSessionHasErrors(['name']);
    }

    public function test_category_search()
    {
        $user = factory(User::class)->states('admin')->create();

        $category = factory(Category::class)->states(['sentence', 'active'])->create();

        // Hook z koniecznosci. Wyszukiwanie odbywa się przez AGAINST MATCH po indeksie,
        // a DatabaseTransactions nie indeksuje ostatnio dodanego modelu.
        DB::statement('OPTIMIZE TABLE categories');

        Auth::login($user, true);

        $response = $this->get(route('admin.category.post.search', [
            'name' => $category->name
        ]));

        $response->assertOk()->assertJsonStructure(['success', 'view']);
        $this->assertStringContainsString($category->name, $response->getData()->view);

        DB::statement('DELETE FROM `categories` WHERE `id` > 0');
    }

    public function test_category_index_as_guest()
    {
        $response = $this->get(route('admin.category.post.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_category_index_without_permission()
    {
        $user = factory(User::class)->create();

        Auth::login($user, true);

        $response = $this->get(route('admin.category.post.index'));

        $response->assertStatus(403);
    }

    public function test_category_index_paginate()
    {
        $user = factory(User::class)->states('admin')->create();

        $category = factory(Category::class, 50)->states('active')->create();

        Auth::login($user, true);

        $response = $this->get(route('admin.category.post.index', [
            'parent' => 0,
            'orderby' => 'created_at|desc'
        ]));

        $response->assertViewIs('icore::admin.category.index');
        $response->assertSee('class="pagination"');
        $response->assertSeeInOrder([$category[30]->title, $category[30]->shortContent]);
    }

    public function test_category_updateStatus_as_guest()
    {
        $response = $this->patch(route('admin.category.update_status', [43]));

        $response->assertRedirect(route('login'));
    }

    public function test_category_updateStatus_without_permission()
    {
        $user = factory(User::class)->create();

        $category = factory(Category::class)->states('active')->create();

        Auth::login($user, true);

        $response = $this->patch(route('admin.category.update_status', [$category->id]));

        $response->assertStatus(403);
    }

    public function test_noexist_category_updateStatus()
    {
        $user = factory(User::class)->states('admin')->create();

        Auth::login($user, true);

        $response = $this->patch(route('admin.category.update_status', [2327382]));

        $response->assertStatus(404);
    }

    public function test_category_updateStatus_validation_fail()
    {
        $user = factory(User::class)->states('admin')->create();

        $category = factory(Category::class)->states('active')->create();

        Auth::login($user, true);

        $response = $this->patch(route('admin.category.update_status', [$category->id]), [
            'status' => 323,
        ]);

        $response->assertSessionHasErrors(['status']);

        $this->assertTrue(Auth::check());
    }

    public function test_category_updateStatus()
    {
        $user = factory(User::class)->states('admin')->create();

        $category = factory(Category::class)->states('active')->create();

        Auth::login($user, true);

        $response = $this->patch(route('admin.category.update_status', [$category->id]), [
            'status' => 0,
        ]);

        $response->assertOk()->assertJsonStructure(['success', 'ancestors', 'descendants']);

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'status' => 0,
        ]);

        $this->assertTrue(Auth::check());
    }

    public function test_category_edit_as_guest()
    {
        $response = $this->get(route('admin.category.edit', [99]));

        $response->assertRedirect(route('login'));
    }

    public function test_category_edit_without_permission()
    {
        $user = factory(User::class)->create();

        $category = factory(Category::class)->states('active')->create();

        Auth::login($user, true);

        $response = $this->get(route('admin.category.edit', [$category->id]));

        $response->assertStatus(403);
    }

    public function test_noexist_category_edit()
    {
        $user = factory(User::class)->states('admin')->create();

        Auth::login($user, true);

        $response = $this->get(route('admin.category.edit', [9999]));

        $response->assertStatus(404);

        $this->assertTrue(Auth::check());
    }

    public function test_category_edit()
    {
        $user = factory(User::class)->states('admin')->create();

        $category = factory(Category::class)->states('active')->create();

        Auth::login($user, true);

        $response = $this->get(route('admin.category.edit', [$category->id]));

        $response->assertOk()->assertJsonStructure(['success', 'view']);
        $this->assertStringContainsString(route('admin.category.update', [$category->id]), $response->getData()->view);
        $this->assertStringContainsString($category->name, $response->getData()->view);

        $this->assertTrue(Auth::check());
    }

    public function test_category_update_as_guest()
    {
        $response = $this->put(route('admin.category.update', [99]), []);

        $response->assertRedirect(route('login'));
    }

    public function test_category_update_without_permission()
    {
        $user = factory(User::class)->create();

        $category = factory(Category::class)->states('active')->create();

        Auth::login($user, true);

        $response = $this->put(route('admin.category.update', [$category->id]));

        $response->assertStatus(403);
    }

    public function test_noexist_category_update()
    {
        $user = factory(User::class)->states('admin')->create();

        Auth::login($user, true);

        $response = $this->put(route('admin.category.update', [9999]), []);

        $response->assertStatus(404);

        $this->assertTrue(Auth::check());
    }

    public function test_category_update_validation_fail()
    {
        $user = factory(User::class)->states('admin')->create();

        $category = factory(Category::class)->states('active')->create();

        Auth::login($user, true);

        $response = $this->put(route('admin.category.update', [$category->id]), [
            'name' => ''
        ]);

        $response->assertSessionHasErrors(['name']);

        $this->assertTrue(Auth::check());
    }

    public function test_root_category_update()
    {
        $user = factory(User::class)->states('admin')->create();

        $category = factory(Category::class)->states('active')->create();

        Auth::login($user, true);

        $response = $this->put(route('admin.category.update', [$category->id]), [
            'name' => '<b>Kategoria</b> <script>Testowa</script>'
        ]);

        $response->assertOk()->assertJsonStructure(['success', 'view']);
        $this->assertStringContainsString('Kategoria Testowa', $response->getData()->view);

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Kategoria Testowa',
            'parent_id' => null
        ]);

        $this->assertTrue(Auth::check());
    }

    public function test_children_category_update()
    {
        $user = factory(User::class)->states('admin')->create();

        $parent = factory(Category::class)->states('active')->create();

        $category = factory(Category::class)->states('active')->create();

        Auth::login($user, true);

        $response = $this->put(route('admin.category.update', [$category->id]), [
            'name' => '<b>Kategoria</b> <script>Testowa</script>',
            'parent_id' => $parent->id
        ]);

        $response->assertOk()->assertJsonStructure(['success', 'view']);
        $this->assertStringContainsString('Kategoria Testowa', $response->getData()->view);

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Kategoria Testowa',
            'parent_id' => $parent->id
        ]);

        $this->assertDatabaseHas('categories_closure', [
            'descendant' => $category->id,
            'ancestor' => $parent->id,
            'depth' => 1
        ]);

        $this->assertTrue(Auth::check());
    }

    public function test_category_post_create_as_guest()
    {
        $response = $this->get(route('admin.category.post.create'));

        $response->assertRedirect(route('login'));
    }

    public function test_category_create_without_permission()
    {
        $user = factory(User::class)->create();

        Auth::login($user, true);

        $response = $this->get(route('admin.category.post.create'));

        $response->assertStatus(403);
    }

    public function test_category_post_create()
    {
        $user = factory(User::class)->states('admin')->create();

        Auth::login($user, true);

        $response = $this->get(route('admin.category.post.create'));

        $response->assertOk()->assertJsonStructure(['success', 'view']);
        $this->assertStringContainsString(route('admin.category.post.store'), $response->getData()->view);

        $this->assertTrue(Auth::check());
    }

    public function test_category_post_store_as_guest()
    {
        $response = $this->post(route('admin.category.post.store'), []);

        $response->assertRedirect(route('login'));
    }

    public function test_category_post_store_without_permission()
    {
        $user = factory(User::class)->create();

        Auth::login($user, true);

        $response = $this->post(route('admin.category.post.store'));

        $response->assertStatus(403);
    }

    public function test_root_category_post_store()
    {
        $user = factory(User::class)->states('admin')->create();

        Auth::login($user, true);

        $response = $this->post(route('admin.category.post.store'), [
            'name' => '<b>Kategoria</b> <script>OK</script>'
        ]);

        $response->assertOk()->assertJsonStructure(['success']);
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('categories', [
            'name' => 'Kategoria OK',
            'parent_id' => null,
            'model_type' => 'N1ebieski\\ICore\\Models\\Post'
        ]);

        $this->assertTrue(Auth::check());
    }

    public function test_children_category_post_store()
    {
        $user = factory(User::class)->states('admin')->create();

        $parent = factory(Category::class)->states('active')->create();

        Auth::login($user, true);

        $response = $this->post(route('admin.category.post.store'), [
            'name' => '<b>Kategoria</b> <script>OK</script>',
            'parent_id' => $parent->id
        ]);

        $response->assertOk()->assertJsonStructure(['success']);
        $response->assertSessionHas('success');

        $category = Category::where([
            ['name', 'Kategoria OK'],
            ['parent_id', $parent->id]
        ])->first();

        $this->assertTrue($category->exists());

        $this->assertDatabaseHas('categories_closure', [
            'descendant' => $category->id,
            'ancestor' => $parent->id,
            'depth' => 1
        ]);

        $this->assertTrue(Auth::check());
    }

    public function test_category_post_storeGlobal_as_guest()
    {
        $response = $this->post(route('admin.category.post.store_global'), []);

        $response->assertRedirect(route('login'));
    }

    public function test_category_post_storeGlobal_without_permission()
    {
        $user = factory(User::class)->create();

        Auth::login($user, true);

        $response = $this->post(route('admin.category.post.store_global'));

        $response->assertStatus(403);
    }

    public function test_category_post_storeGlobal_validation_fail()
    {
        $user = factory(User::class)->states('admin')->create();

        Auth::login($user, true);

        $response = $this->post(route('admin.category.post.store_global'), [
            'names' => '',
        ]);

        $response->assertSessionHasErrors(['names']);

        $this->assertTrue(Auth::check());
    }

    public function test_category_post_storeGlobal()
    {
        $user = factory(User::class)->states('admin')->create();

        $parent = factory(Category::class)->states('active')->create();

        Auth::login($user, true);

        $response = $this->post(route('admin.category.post.store_global'), [
            'names' => '
            [
              {
                "name": "Hdsajdhajs",
                "children": [
                  {
                    "name": "Dziecko 1"
                  },
                  {
                    "name": "Dziecko 2"
                  }
                ]
              },
              {
                "name": "Gumboszek"
              }
            ]
            ',
            'parent_id' => $parent->id
        ]);

        $response->assertOk()->assertJsonStructure(['success']);
        $response->assertSessionHas('success');

        $category = Category::where([
            ['name', 'Dziecko 1']
        ])->first();

        $this->assertTrue($category->exists());

        $this->assertDatabaseHas('categories_closure', [
            'descendant' => $category->id,
            'ancestor' => $parent->id,
            'depth' => 2
        ]);

        $this->assertTrue(Auth::check());
    }

    public function test_category_destroy_as_guest()
    {
        $response = $this->delete(route('admin.category.destroy', [43]));

        $response->assertRedirect(route('login'));
    }

    public function test_category_destroy_without_permission()
    {
        $user = factory(User::class)->create();

        $category = factory(Category::class)->states('active')->create();

        Auth::login($user, true);

        $response = $this->delete(route('admin.category.destroy', [$category->id]));

        $response->assertStatus(403);
    }

    public function test_noexist_category_destroy()
    {
        $user = factory(User::class)->states('admin')->create();

        Auth::login($user, true);

        $response = $this->delete(route('admin.category.destroy', [2327382]));

        $response->assertStatus(404);
    }

    public function test_category_destroy()
    {
        $user = factory(User::class)->states('admin')->create();

        $category = factory(Category::class)->states('active')->create();

        Auth::login($user, true);

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
        ]);

        $response = $this->delete(route('admin.category.destroy', [$category->id]), []);

        $response->assertOk()->assertJsonStructure(['success']);

        $this->assertDatabaseMissing('categories', [
            'id' => $category->id,
        ]);

        $this->assertTrue(Auth::check());
    }

    public function test_category_destroyGlobal_as_guest()
    {
        $response = $this->delete(route('admin.category.destroy_global'), []);

        $response->assertRedirect(route('login'));
    }

    public function test_category_destroyGlobal_without_permission()
    {
        $user = factory(User::class)->create();

        Auth::login($user, true);

        $response = $this->delete(route('admin.category.destroy_global'), []);

        $response->assertStatus(403);
    }

    public function test_category_destroyGlobal_validation_fail()
    {
        $user = factory(User::class)->states('admin')->create();

        Auth::login($user, true);

        $response = $this->delete(route('admin.category.destroy_global', [
            'dasdas',
            'dada'
        ]));

        $response->assertSessionHasErrors(['select']);
    }

    public function test_category_destroyGlobal()
    {
        $user = factory(User::class)->states('admin')->create();

        $category = factory(Category::class, 10)->states('active')->create();

        Auth::login($user, true);

        $this->get(route('admin.category.post.index'));

        $select = collect($category)->pluck('id')->take(5)->toArray();

        $response = $this->delete(route('admin.category.destroy_global'), [
            'select' => $select,
        ]);

        $response->assertRedirect(route('admin.category.post.index'));
        $response->assertSessionHas('success');

        $deleted = Category::whereIn('id', $select)->get();

        $this->assertTrue($deleted->count() === 0);

        $this->assertTrue(Auth::check());
    }

    public function test_category_edit_position_as_guest()
    {
        $response = $this->get(route('admin.category.edit_position', [43]));

        $response->assertRedirect(route('login'));
    }

    public function test_category_edit_position_without_permission()
    {
        $user = factory(User::class)->create();

        $category = factory(Category::class)->states('active')->create();

        Auth::login($user, true);

        $response = $this->get(route('admin.category.edit_position', [$category->id]));

        $response->assertStatus(403);
    }

    public function test_noexist_category_edit_position()
    {
        $user = factory(User::class)->states('admin')->create();

        Auth::login($user, true);

        $response = $this->get(route('admin.category.edit_position', [2327382]));

        $response->assertStatus(404);
    }

    public function test_category_edit_position()
    {
        $user = factory(User::class)->states('admin')->create();

        $category = factory(Category::class)->states('active')->create();

        Auth::login($user, true);

        $response = $this->get(route('admin.category.edit_position', [$category->id]));

        $response->assertOk()->assertJsonStructure(['success', 'view']);
        $this->assertStringContainsString('value="'.$category->position.'"', $response->getData()->view);
        $this->assertStringContainsString(route('admin.category.update_position', [$category->id]), $response->getData()->view);

    }

    public function test_category_update_position_as_guest()
    {
        $response = $this->patch(route('admin.category.update_position', [2323]));

        $response->assertRedirect(route('login'));
    }

    public function test_category_update_position_without_permission()
    {
        $user = factory(User::class)->create();

        $category = factory(Category::class)->states('active')->create();

        Auth::login($user, true);

        $response = $this->patch(route('admin.category.update_position', [$category->id]));

        $response->assertStatus(403);
    }

    public function test_noexist_category_update_position()
    {
        $user = factory(User::class)->states('admin')->create();

        Auth::login($user, true);

        $response = $this->patch(route('admin.category.update_position', [2327382]));

        $response->assertStatus(404);
    }

    public function test_category_update_position_validation_fail()
    {
        $user = factory(User::class)->states('admin')->create();

        $category = factory(Category::class)->states('active')->create();

        Auth::login($user, true);

        $response = $this->patch(route('admin.category.update_position', [$category->id]), [
            'position' => 1232
        ]);

        $response->assertSessionHasErrors(['position']);

        $this->assertTrue(Auth::check());
    }

    public function test_category_update_position()
    {
        $user = factory(User::class)->states('admin')->create();

        $category = factory(Category::class, 3)->states('active')->create();

        $this->assertDatabaseHas('categories', [
            'id' => $category[0]->id,
            'position' => 0
        ]);

        $this->assertDatabaseHas('categories', [
            'id' => $category[2]->id,
            'position' => 2
        ]);

        Auth::login($user, true);

        $response = $this->patch(route('admin.category.update_position', [$category[2]->id]), [
            'position' => 0
        ]);

        $this->assertDatabaseHas('categories', [
            'id' => $category[2]->id,
            'position' => 0
        ]);

        $this->assertDatabaseHas('categories', [
            'id' => $category[0]->id,
            'position' => 1
        ]);

        $this->assertTrue(Auth::check());
    }
}
