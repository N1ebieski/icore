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

namespace N1ebieski\ICore\Tests\Integration\Link\Link;

use Tests\TestCase;
use N1ebieski\ICore\Models\Link;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class MultiLangTest extends TestCase
{
    use DatabaseTransactions;

    /**
     *
     * @return void
     * @throws BindingResolutionException
     */
    protected function setUp(): void
    {
        parent::setUp();

        Config::set('icore.multi_langs', ['pl', 'en']);
    }

    public function testSiblings(): void
    {
        foreach (['pl', 'en'] as $lang) {
            /** @var array<array<Link>> */
            $links[$lang] = Link::makeFactory()->link()->count(rand(2, 5))->create([
                'lang' => $lang
            ]);
        }

        App::setLocale('pl');

        /** @var Link */
        $link = $links['pl'][0];

        $this->assertContains($links['pl'][1]->id, $link->siblings->pluck('id')->toArray());
        $this->assertNotContains($links['en'][1]->id, $link->siblings->pluck('id')->toArray());
    }
}
