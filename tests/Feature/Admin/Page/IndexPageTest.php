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

namespace N1ebieski\ICore\Tests\Feature\Admin\Page;

use Tests\TestCase;
use N1ebieski\ICore\Models\User;
use Illuminate\Support\Facades\Auth;
use N1ebieski\ICore\Models\Page\Page;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class IndexPageTest extends TestCase
{
    use DatabaseTransactions;

    public function testIndexAsGuest(): void
    {
        $response = $this->get(route('admin.page.index'));

        $response->assertRedirect(route('login'));
    }

    public function testIndexWithoutPermission(): void
    {
        /**
         * @var User
         */
        $user = User::makeFactory()->create();

        Auth::login($user);

        $response = $this->get(route('admin.page.index'));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testIndexPaginate(): void
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
}
