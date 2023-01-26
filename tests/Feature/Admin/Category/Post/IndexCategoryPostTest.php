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
use Illuminate\Support\Carbon;
use N1ebieski\ICore\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Response as HttpResponse;
use N1ebieski\ICore\Models\Category\Post\Category;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class IndexCategoryPostTest extends TestCase
{
    use DatabaseTransactions;

    public function testIndexAsGuest(): void
    {
        $response = $this->get(route('admin.category.post.index'));

        $response->assertRedirect(route('login'));
    }

    public function testIndexWithoutPermission(): void
    {
        /** @var User */
        $user = User::makeFactory()->create();

        Auth::login($user);

        $response = $this->get(route('admin.category.post.index'));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testIndexPaginate(): void
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
}
