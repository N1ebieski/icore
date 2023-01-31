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

namespace N1ebieski\ICore\Tests\Feature\Admin\Category\Post;

use Tests\TestCase;
use N1ebieski\ICore\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response as HttpResponse;
use N1ebieski\ICore\Models\Category\Post\Category;
use N1ebieski\ICore\Models\CategoryLang\CategoryLang;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CreateCategoryTest extends TestCase
{
    use DatabaseTransactions;

    public function testCreateAsGuest(): void
    {
        $response = $this->get(route('admin.category.post.create'));

        $response->assertRedirect(route('login'));
    }

    public function testCreateWithoutPermission(): void
    {
        /** @var User */
        $user = User::makeFactory()->create();

        Auth::login($user);

        $response = $this->get(route('admin.category.post.create'));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testCreate(): void
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

    public function testStoreAsGuest(): void
    {
        $response = $this->post(route('admin.category.post.store'), []);

        $response->assertRedirect(route('login'));
    }

    public function testStoreWithoutPermission(): void
    {
        /** @var User */
        $user = User::makeFactory()->create();

        Auth::login($user);

        $response = $this->post(route('admin.category.post.store'));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testStoreRoot(): void
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

    public function testStoreChildren(): void
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

    public function testStoreGlobalAsGuest(): void
    {
        $response = $this->post(route('admin.category.post.store_global'), []);

        $response->assertRedirect(route('login'));
    }

    public function testStoreGlobalWithoutPermission(): void
    {
        /** @var User */
        $user = User::makeFactory()->create();

        Auth::login($user);

        $response = $this->post(route('admin.category.post.store_global'));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testStoreGlobalValidationFail(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        Auth::login($user);

        $response = $this->post(route('admin.category.post.store_global'), [
            'names' => '',
        ]);

        $response->assertSessionHasErrors(['names']);
    }

    public function testStoreGlobal(): void
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
}
