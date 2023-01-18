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
use Illuminate\Support\Carbon;
use N1ebieski\ICore\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Response as HttpResponse;
use N1ebieski\ICore\ValueObjects\Category\Status;
use N1ebieski\ICore\Models\Category\Post\Category;
use Illuminate\Database\Eloquent\Factories\Sequence;
use N1ebieski\ICore\Models\CategoryLang\CategoryLang;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CategoryTest extends TestCase
{
    use DatabaseTransactions;

    public function testCategoryIndexAsGuest(): void
    {
        $response = $this->get(route('admin.category.post.index'));

        $response->assertRedirect(route('login'));
    }

    public function testCategoryIndexWithoutPermission(): void
    {
        /** @var User */
        $user = User::makeFactory()->create();

        Auth::login($user);

        $response = $this->get(route('admin.category.post.index'));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testCategoryIndexPaginate(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        /** @var Collection<Category>|array<Category> */
        $categories = Category::makeFactory()->count(50)
            ->sequence(function (Sequence $sequence) {
                return [
                    'created_at' => Carbon::now()->addSeconds($sequence->index)
                ];
            })
            ->active()
            ->create();

        Auth::login($user);

        $response = $this->get(route('admin.category.post.index', [
            'page' => 2,
            'filter' => [
                'parent' => 0,
                'orderby' => 'created_at|asc'
            ]
        ]));

        $response->assertViewIs('icore::admin.category.index');
        $response->assertSee('class="pagination"', false);
        $response->assertSeeInOrder([$categories[30]->name], false);
    }

    public function testCategoryUpdateStatusAsGuest(): void
    {
        $response = $this->patch(route('admin.category.update_status', [43]));

        $response->assertRedirect(route('login'));
    }

    public function testCategoryUpdateStatusWithoutPermission(): void
    {
        /** @var User */
        $user = User::makeFactory()->create();

        /** @var Category */
        $category = Category::makeFactory()->active()->create();

        Auth::login($user);

        $response = $this->patch(route('admin.category.update_status', [$category->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testNoexistCategoryUpdateStatus(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        Auth::login($user);

        $response = $this->patch(route('admin.category.update_status', [2327382]));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testCategoryUpdateStatusValidationFail(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        /** @var Category */
        $category = Category::makeFactory()->active()->create();

        Auth::login($user);

        $response = $this->patch(route('admin.category.update_status', [$category->id]), [
            'status' => 323,
        ]);

        $response->assertSessionHasErrors(['status']);
    }

    public function testCategoryUpdateStatus(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        /** @var Category */
        $category = Category::makeFactory()->active()->create();

        Auth::login($user);

        $response = $this->patch(route('admin.category.update_status', [$category->id]), [
            'status' => Status::INACTIVE,
        ]);

        $response->assertOk();
        $response->assertJsonStructure(['ancestors', 'descendants']);

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'status' => Status::INACTIVE,
        ]);
    }

    public function testCategoryEditAsGuest(): void
    {
        $response = $this->get(route('admin.category.edit', [99]));

        $response->assertRedirect(route('login'));
    }

    public function testCategoryEditWithoutPermission(): void
    {
        /** @var User */
        $user = User::makeFactory()->create();

        /** @var Category */
        $category = Category::makeFactory()->active()->create();

        Auth::login($user);

        $response = $this->get(route('admin.category.edit', [$category->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testNoexistCategoryEdit(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        Auth::login($user);

        $response = $this->get(route('admin.category.edit', [9999]));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testCategoryEdit(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        /** @var Category */
        $category = Category::makeFactory()->active()->create();

        Auth::login($user);

        $response = $this->get(route('admin.category.edit', [$category->id]));

        $response->assertOk();
        $response->assertJsonStructure(['view']);

        /** @var JsonResponse */
        $baseResponse = $response->baseResponse;

        $this->assertStringContainsString(
            route('admin.category.update', [$category->id]),
            $baseResponse->getData()->view
        );
        $this->assertStringContainsString(
            $category->name,
            $baseResponse->getData()->view
        );
    }

    public function testCategoryUpdateAsGuest(): void
    {
        $response = $this->put(route('admin.category.update', [99]), []);

        $response->assertRedirect(route('login'));
    }

    public function testCategoryUpdateWithoutPermission(): void
    {
        /** @var User */
        $user = User::makeFactory()->create();

        /** @var Category */
        $category = Category::makeFactory()->active()->create();

        Auth::login($user);

        $response = $this->put(route('admin.category.update', [$category->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testNoexistCategoryUpdate(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        Auth::login($user);

        $response = $this->put(route('admin.category.update', [9999]), []);

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testCategoryUpdateValidationFail(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        /** @var Category */
        $category = Category::makeFactory()->active()->create();

        Auth::login($user);

        $response = $this->put(route('admin.category.update', [$category->id]), [
            'name' => ''
        ]);

        $response->assertSessionHasErrors(['name']);
    }

    public function testRootCategoryUpdate(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        /** @var Category */
        $category = Category::makeFactory()->active()->create();

        Auth::login($user);

        $response = $this->put(route('admin.category.update', [$category->id]), [
            'name' => '<b>Kategoria</b> <script>Testowa</script>'
        ]);

        $response->assertOk();
        $response->assertJsonStructure(['view']);

        /** @var JsonResponse */
        $baseResponse = $response->baseResponse;

        $this->assertStringContainsString(
            'Kategoria Testowa',
            $baseResponse->getData()->view
        );

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'parent_id' => null
        ]);

        $this->assertDatabaseHas('categories_langs', [
            'category_id' => $category->id,
            'name' => 'Kategoria Testowa'
        ]);
    }

    public function testChildrenCategoryUpdate(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        /** @var Category */
        $parent = Category::makeFactory()->active()->create();

        /** @var Category */
        $category = Category::makeFactory()->active()->create();

        Auth::login($user);

        $response = $this->put(route('admin.category.update', [$category->id]), [
            'name' => '<b>Kategoria</b> <script>Testowa</script>',
            'parent_id' => $parent->id
        ]);

        $response->assertOk();
        $response->assertJsonStructure(['view']);

        /** @var JsonResponse */
        $baseResponse = $response->baseResponse;

        $this->assertStringContainsString(
            'Kategoria Testowa',
            $baseResponse->getData()->view
        );

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'parent_id' => $parent->id
        ]);

        $this->assertDatabaseHas('categories_langs', [
            'category_id' => $category->id,
            'name' => 'Kategoria Testowa',
        ]);

        $this->assertDatabaseHas('categories_closure', [
            'descendant' => $category->id,
            'ancestor' => $parent->id,
            'depth' => 1
        ]);
    }

    public function testCategoryPostCreateAsGuest(): void
    {
        $response = $this->get(route('admin.category.post.create'));

        $response->assertRedirect(route('login'));
    }

    public function testCategoryCreateWithoutPermission(): void
    {
        /** @var User */
        $user = User::makeFactory()->create();

        Auth::login($user);

        $response = $this->get(route('admin.category.post.create'));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testCategoryPostCreate(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        Auth::login($user);

        $response = $this->get(route('admin.category.post.create'));

        $response->assertOk();
        $response->assertJsonStructure(['view']);

        /** @var JsonResponse */
        $baseResponse = $response->baseResponse;

        $this->assertStringContainsString(
            route('admin.category.post.store'),
            $baseResponse->getData()->view
        );
    }

    public function testCategoryPostStoreAsGuest(): void
    {
        $response = $this->post(route('admin.category.post.store'), []);

        $response->assertRedirect(route('login'));
    }

    public function testCategoryPostStoreWithoutPermission(): void
    {
        /** @var User */
        $user = User::makeFactory()->create();

        Auth::login($user);

        $response = $this->post(route('admin.category.post.store'));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testRootCategoryPostStore(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        Auth::login($user);

        $response = $this->post(route('admin.category.post.store'), [
            'name' => '<b>Kategoria</b> <script>OK</script>'
        ]);

        $response->assertOk();
        $response->assertSessionHas('success');

        /** @var CategoryLang|null */
        $categoryLang = CategoryLang::where('name', 'Kategoria OK')->first();

        $this->assertTrue($categoryLang?->exists());

        $this->assertDatabaseHas('categories', [
            'id' => $categoryLang->category_id,
            'parent_id' => null,
            'model_type' => 'N1ebieski\\ICore\\Models\\Post'
        ]);
    }

    public function testChildrenCategoryPostStore(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        /** @var Category */
        $parent = Category::makeFactory()->active()->create();

        Auth::login($user);

        $response = $this->post(route('admin.category.post.store'), [
            'name' => '<b>Kategoria</b> <script>OK</script>',
            'parent_id' => $parent->id
        ]);

        $response->assertOk();
        $response->assertSessionHas('success');

        /** @var CategoryLang|null */
        $categoryLang = CategoryLang::where('name', 'Kategoria OK')->first();

        $this->assertTrue($categoryLang?->exists());

        $this->assertDatabaseHas('categories_closure', [
            'descendant' => $categoryLang->category_id,
            'ancestor' => $parent->id,
            'depth' => 1
        ]);
    }

    public function testCategoryPostStoreGlobalAsGuest(): void
    {
        $response = $this->post(route('admin.category.post.store_global'), []);

        $response->assertRedirect(route('login'));
    }

    public function testCategoryPostStoreGlobalWithoutPermission(): void
    {
        /** @var User */
        $user = User::makeFactory()->create();

        Auth::login($user);

        $response = $this->post(route('admin.category.post.store_global'));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testCategoryPostStoreGlobalValidationFail(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        Auth::login($user);

        $response = $this->post(route('admin.category.post.store_global'), [
            'names' => '',
        ]);

        $response->assertSessionHasErrors(['names']);
    }

    public function testCategoryPostStoreGlobal(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        /** @var Category */
        $parent = Category::makeFactory()->active()->create();

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

        $response->assertOk();
        $response->assertSessionHas('success');

        /** @var CategoryLang|null */
        $categoryLang = CategoryLang::where('name', 'Dziecko 1')->first();

        $this->assertTrue($categoryLang?->exists());

        $this->assertDatabaseHas('categories_closure', [
            'descendant' => $categoryLang->category_id,
            'ancestor' => $parent->id,
            'depth' => 2
        ]);
    }

    public function testCategoryDestroyAsGuest(): void
    {
        $response = $this->delete(route('admin.category.destroy', [43]));

        $response->assertRedirect(route('login'));
    }

    public function testCategoryDestroyWithoutPermission(): void
    {
        /** @var User */
        $user = User::makeFactory()->create();

        /** @var Category */
        $category = Category::makeFactory()->active()->create();

        Auth::login($user);

        $response = $this->delete(route('admin.category.destroy', [$category->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testNoexistCategoryDestroy(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        Auth::login($user);

        $response = $this->delete(route('admin.category.destroy', [2327382]));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testCategoryDestroy(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        /** @var Category */
        $category = Category::makeFactory()->active()->create();

        Auth::login($user);

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
        ]);

        $response = $this->delete(route('admin.category.destroy', [$category->id]), []);

        $response->assertOk();

        $this->assertDatabaseMissing('categories', [
            'id' => $category->id,
        ]);
    }

    public function testCategoryDestroyGlobalAsGuest(): void
    {
        $response = $this->delete(route('admin.category.destroy_global'), []);

        $response->assertRedirect(route('login'));
    }

    public function testCategoryDestroyGlobalWithoutPermission(): void
    {
        /** @var User */
        $user = User::makeFactory()->create();

        Auth::login($user);

        $response = $this->delete(route('admin.category.destroy_global'), []);

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testCategoryDestroyGlobalValidationFail(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        Auth::login($user);

        $response = $this->delete(route('admin.category.destroy_global', [
            'dasdas',
            'dada'
        ]));

        $response->assertSessionHasErrors(['select']);
    }

    public function testCategoryDestroyGlobal(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        /** @var Category */
        $category = Category::makeFactory()->count(10)->active()->create();

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
    }

    public function testCategoryEditPositionAsGuest(): void
    {
        $response = $this->get(route('admin.category.edit_position', [43]));

        $response->assertRedirect(route('login'));
    }

    public function testCategoryEditPositionWithoutPermission(): void
    {
        /** @var User */
        $user = User::makeFactory()->create();

        /** @var Category */
        $category = Category::makeFactory()->active()->create();

        Auth::login($user);

        $response = $this->get(route('admin.category.edit_position', [$category->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testNoexistCategoryEditPosition(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        Auth::login($user);

        $response = $this->get(route('admin.category.edit_position', [2327382]));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testCategoryEditPosition(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        /** @var Category */
        $category = Category::makeFactory()->active()->create();

        Auth::login($user);

        $response = $this->get(route('admin.category.edit_position', [$category->id]));

        $response->assertOk();
        $response->assertJsonStructure(['view']);

        /** @var JsonResponse */
        $baseResponse = $response->baseResponse;

        $this->assertStringContainsString(
            'value="' . $category->position . '"',
            $baseResponse->getData()->view
        );
        $this->assertStringContainsString(
            route('admin.category.update_position', [$category->id]),
            $baseResponse->getData()->view
        );
    }

    public function testCategoryUpdatePositionAsGuest(): void
    {
        $response = $this->patch(route('admin.category.update_position', [2323]));

        $response->assertRedirect(route('login'));
    }

    public function testCategoryUpdatePositionWithoutPermission(): void
    {
        /** @var User */
        $user = User::makeFactory()->create();

        /** @var Category */
        $category = Category::makeFactory()->active()->create();

        Auth::login($user);

        $response = $this->patch(route('admin.category.update_position', [$category->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testNoexistCategoryUpdatePosition(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        Auth::login($user);

        $response = $this->patch(route('admin.category.update_position', [2327382]));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testCategoryUpdatePositionValidationFail(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        /** @var Category */
        $category = Category::makeFactory()->active()->create();

        Auth::login($user);

        $response = $this->patch(route('admin.category.update_position', [$category->id]), [
            'position' => 1232
        ]);

        $response->assertSessionHasErrors(['position']);
    }

    public function testCategoryUpdatePosition(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        /** @var Collection<Category>|array<Category> */
        $categories = Category::makeFactory()->count(3)->active()->create();

        $this->assertDatabaseHas('categories', [
            'id' => $categories[0]->id,
            'position' => 0
        ]);

        $this->assertDatabaseHas('categories', [
            'id' => $categories[2]->id,
            'position' => 2
        ]);

        Auth::login($user);

        $this->patch(route('admin.category.update_position', [$categories[2]->id]), [
            'position' => 0
        ]);

        $this->assertDatabaseHas('categories', [
            'id' => $categories[2]->id,
            'position' => 0
        ]);

        $this->assertDatabaseHas('categories', [
            'id' => $categories[0]->id,
            'position' => 1
        ]);
    }
}
