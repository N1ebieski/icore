<?php

namespace N1ebieski\ICore\Tests\Feature\Admin;

use Tests\TestCase;
use N1ebieski\ICore\Models\User;
use Illuminate\Support\Facades\Auth;
use N1ebieski\ICore\Models\Category\Post\Category;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CategoryTest extends TestCase
{
    use DatabaseTransactions;

    public function testCategoryIndexAsGuest()
    {
        $response = $this->get(route('admin.category.post.index'));

        $response->assertRedirect(route('login'));
    }

    public function testCategoryIndexWithoutPermission()
    {
        $user = User::factory()->create();

        Auth::login($user);

        $response = $this->get(route('admin.category.post.index'));

        $response->assertStatus(403);
    }

    public function testCategoryIndexPaginate()
    {
        $user = User::factory()->admin()->create();

        $category = Category::factory()->count(50)->active()->create();

        Auth::login($user);

        $response = $this->get(route('admin.category.post.index', [
            'filter' => [
                'parent' => 0,
                'orderby' => 'created_at|desc'
            ]
        ]));

        $response->assertViewIs('icore::admin.category.index');
        $response->assertSee('class="pagination"', false);
        $response->assertSeeInOrder([$category[30]->title, $category[30]->shortContent], false);
    }

    public function testCategoryUpdateStatusAsGuest()
    {
        $response = $this->patch(route('admin.category.update_status', [43]));

        $response->assertRedirect(route('login'));
    }

    public function testCategoryUpdateStatusWithoutPermission()
    {
        $user = User::factory()->create();

        $category = Category::factory()->active()->create();

        Auth::login($user);

        $response = $this->patch(route('admin.category.update_status', [$category->id]));

        $response->assertStatus(403);
    }

    public function testNoexistCategoryUpdateStatus()
    {
        $user = User::factory()->admin()->create();

        Auth::login($user);

        $response = $this->patch(route('admin.category.update_status', [2327382]));

        $response->assertStatus(404);
    }

    public function testCategoryUpdateStatusValidationFail()
    {
        $user = User::factory()->admin()->create();

        $category = Category::factory()->active()->create();

        Auth::login($user);

        $response = $this->patch(route('admin.category.update_status', [$category->id]), [
            'status' => 323,
        ]);

        $response->assertSessionHasErrors(['status']);

        $this->assertTrue(Auth::check());
    }

    public function testCategoryUpdateStatus()
    {
        $user = User::factory()->admin()->create();

        $category = Category::factory()->active()->create();

        Auth::login($user);

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

    public function testCategoryEditAsGuest()
    {
        $response = $this->get(route('admin.category.edit', [99]));

        $response->assertRedirect(route('login'));
    }

    public function testCategoryEditWithoutPermission()
    {
        $user = User::factory()->create();

        $category = Category::factory()->active()->create();

        Auth::login($user);

        $response = $this->get(route('admin.category.edit', [$category->id]));

        $response->assertStatus(403);
    }

    public function testNoexistCategoryEdit()
    {
        $user = User::factory()->admin()->create();

        Auth::login($user);

        $response = $this->get(route('admin.category.edit', [9999]));

        $response->assertStatus(404);

        $this->assertTrue(Auth::check());
    }

    public function testCategoryEdit()
    {
        $user = User::factory()->admin()->create();

        $category = Category::factory()->active()->create();

        Auth::login($user);

        $response = $this->get(route('admin.category.edit', [$category->id]));

        $response->assertOk()->assertJsonStructure(['success', 'view']);
        $this->assertStringContainsString(route('admin.category.update', [$category->id]), $response->getData()->view);
        $this->assertStringContainsString($category->name, $response->getData()->view);

        $this->assertTrue(Auth::check());
    }

    public function testCategoryUpdateAsGuest()
    {
        $response = $this->put(route('admin.category.update', [99]), []);

        $response->assertRedirect(route('login'));
    }

    public function testCategoryUpdateWithoutPermission()
    {
        $user = User::factory()->create();

        $category = Category::factory()->active()->create();

        Auth::login($user);

        $response = $this->put(route('admin.category.update', [$category->id]));

        $response->assertStatus(403);
    }

    public function testNoexistCategoryUpdate()
    {
        $user = User::factory()->admin()->create();

        Auth::login($user);

        $response = $this->put(route('admin.category.update', [9999]), []);

        $response->assertStatus(404);

        $this->assertTrue(Auth::check());
    }

    public function testCategoryUpdateValidationFail()
    {
        $user = User::factory()->admin()->create();

        $category = Category::factory()->active()->create();

        Auth::login($user);

        $response = $this->put(route('admin.category.update', [$category->id]), [
            'name' => ''
        ]);

        $response->assertSessionHasErrors(['name']);

        $this->assertTrue(Auth::check());
    }

    public function testRootCategoryUpdate()
    {
        $user = User::factory()->admin()->create();

        $category = Category::factory()->active()->create();

        Auth::login($user);

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

    public function testChildrenCategoryUpdate()
    {
        $user = User::factory()->admin()->create();

        $parent = Category::factory()->active()->create();

        $category = Category::factory()->active()->create();

        Auth::login($user);

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

    public function testCategoryPostCreateAsGuest()
    {
        $response = $this->get(route('admin.category.post.create'));

        $response->assertRedirect(route('login'));
    }

    public function testCategoryCreateWithoutPermission()
    {
        $user = User::factory()->create();

        Auth::login($user);

        $response = $this->get(route('admin.category.post.create'));

        $response->assertStatus(403);
    }

    public function testCategoryPostCreate()
    {
        $user = User::factory()->admin()->create();

        Auth::login($user);

        $response = $this->get(route('admin.category.post.create'));

        $response->assertOk()->assertJsonStructure(['success', 'view']);
        $this->assertStringContainsString(route('admin.category.post.store'), $response->getData()->view);

        $this->assertTrue(Auth::check());
    }

    public function testCategoryPostStoreAsGuest()
    {
        $response = $this->post(route('admin.category.post.store'), []);

        $response->assertRedirect(route('login'));
    }

    public function testCategoryPostStoreWithoutPermission()
    {
        $user = User::factory()->create();

        Auth::login($user);

        $response = $this->post(route('admin.category.post.store'));

        $response->assertStatus(403);
    }

    public function testRootCategoryPostStore()
    {
        $user = User::factory()->admin()->create();

        Auth::login($user);

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

    public function testChildrenCategoryPostStore()
    {
        $user = User::factory()->admin()->create();

        $parent = Category::factory()->active()->create();

        Auth::login($user);

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

    public function testCategoryPostStoreGlobalAsGuest()
    {
        $response = $this->post(route('admin.category.post.store_global'), []);

        $response->assertRedirect(route('login'));
    }

    public function testCategoryPostStoreGlobalWithoutPermission()
    {
        $user = User::factory()->create();

        Auth::login($user);

        $response = $this->post(route('admin.category.post.store_global'));

        $response->assertStatus(403);
    }

    public function testCategoryPostStoreGlobalValidationFail()
    {
        $user = User::factory()->admin()->create();

        Auth::login($user);

        $response = $this->post(route('admin.category.post.store_global'), [
            'names' => '',
        ]);

        $response->assertSessionHasErrors(['names']);

        $this->assertTrue(Auth::check());
    }

    public function testCategoryPostStoreGlobal()
    {
        $user = User::factory()->admin()->create();

        $parent = Category::factory()->active()->create();

        Auth::login($user);

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

    public function testCategoryDestroyAsGuest()
    {
        $response = $this->delete(route('admin.category.destroy', [43]));

        $response->assertRedirect(route('login'));
    }

    public function testCategoryDestroyWithoutPermission()
    {
        $user = User::factory()->create();

        $category = Category::factory()->active()->create();

        Auth::login($user);

        $response = $this->delete(route('admin.category.destroy', [$category->id]));

        $response->assertStatus(403);
    }

    public function testNoexistCategoryDestroy()
    {
        $user = User::factory()->admin()->create();

        Auth::login($user);

        $response = $this->delete(route('admin.category.destroy', [2327382]));

        $response->assertStatus(404);
    }

    public function testCategoryDestroy()
    {
        $user = User::factory()->admin()->create();

        $category = Category::factory()->active()->create();

        Auth::login($user);

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

    public function testCategoryDestroyGlobalAsGuest()
    {
        $response = $this->delete(route('admin.category.destroy_global'), []);

        $response->assertRedirect(route('login'));
    }

    public function testCategoryDestroyGlobalWithoutPermission()
    {
        $user = User::factory()->create();

        Auth::login($user);

        $response = $this->delete(route('admin.category.destroy_global'), []);

        $response->assertStatus(403);
    }

    public function testCategoryDestroyGlobalValidationFail()
    {
        $user = User::factory()->admin()->create();

        Auth::login($user);

        $response = $this->delete(route('admin.category.destroy_global', [
            'dasdas',
            'dada'
        ]));

        $response->assertSessionHasErrors(['select']);
    }

    public function testCategoryDestroyGlobal()
    {
        $user = User::factory()->admin()->create();

        $category = Category::factory()->count(10)->active()->create();

        Auth::login($user);

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

    public function testCategoryEditPositionAsGuest()
    {
        $response = $this->get(route('admin.category.edit_position', [43]));

        $response->assertRedirect(route('login'));
    }

    public function testCategoryEditPositionWithoutPermission()
    {
        $user = User::factory()->create();

        $category = Category::factory()->active()->create();

        Auth::login($user);

        $response = $this->get(route('admin.category.edit_position', [$category->id]));

        $response->assertStatus(403);
    }

    public function testNoexistCategoryEditPosition()
    {
        $user = User::factory()->admin()->create();

        Auth::login($user);

        $response = $this->get(route('admin.category.edit_position', [2327382]));

        $response->assertStatus(404);
    }

    public function testCategoryEditPosition()
    {
        $user = User::factory()->admin()->create();

        $category = Category::factory()->active()->create();

        Auth::login($user);

        $response = $this->get(route('admin.category.edit_position', [$category->id]));

        $response->assertOk()->assertJsonStructure(['success', 'view']);
        $this->assertStringContainsString('value="' . $category->position . '"', $response->getData()->view);
        $this->assertStringContainsString(route('admin.category.update_position', [$category->id]), $response->getData()->view);
    }

    public function testCategoryUpdatePositionAsGuest()
    {
        $response = $this->patch(route('admin.category.update_position', [2323]));

        $response->assertRedirect(route('login'));
    }

    public function testCategoryUpdatePositionWithoutPermission()
    {
        $user = User::factory()->create();

        $category = Category::factory()->active()->create();

        Auth::login($user);

        $response = $this->patch(route('admin.category.update_position', [$category->id]));

        $response->assertStatus(403);
    }

    public function testNoexistCategoryUpdatePosition()
    {
        $user = User::factory()->admin()->create();

        Auth::login($user);

        $response = $this->patch(route('admin.category.update_position', [2327382]));

        $response->assertStatus(404);
    }

    public function testCategoryUpdatePositionValidationFail()
    {
        $user = User::factory()->admin()->create();

        $category = Category::factory()->active()->create();

        Auth::login($user);

        $response = $this->patch(route('admin.category.update_position', [$category->id]), [
            'position' => 1232
        ]);

        $response->assertSessionHasErrors(['position']);

        $this->assertTrue(Auth::check());
    }

    public function testCategoryUpdatePosition()
    {
        $user = User::factory()->admin()->create();

        $category = Category::factory()->count(3)->active()->create();

        $this->assertDatabaseHas('categories', [
            'id' => $category[0]->id,
            'position' => 0
        ]);

        $this->assertDatabaseHas('categories', [
            'id' => $category[2]->id,
            'position' => 2
        ]);

        Auth::login($user);

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
