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
use N1ebieski\ICore\Models\Category\Post\Category;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UpdateCategoryPostTest extends TestCase
{
    use DatabaseTransactions;

    public function testEdit(): void
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

    public function testUpdateRoot(): void
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

    public function testUpdateChildren(): void
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
}
