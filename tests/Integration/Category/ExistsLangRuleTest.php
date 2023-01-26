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

namespace N1ebieski\ICore\Tests\Integration\Category;

use Tests\TestCase;
use Category\Status;
use Illuminate\Support\Facades\App;
use N1ebieski\ICore\Rules\ExistsLangRule;
use N1ebieski\ICore\Models\Category\Category;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ExistsLangRuleTest extends TestCase
{
    use DatabaseTransactions;

    public function testNoExists(): void
    {
        $category = new Category();

        /** @var ExistsLangRule */
        $rule = App::make(ExistsLangRule::class, [
            'table' => $category->getTable(),
            'column' => 'id'
        ]);

        $this->assertFalse($rule->passes('id', 47));
    }

    public function testNoExistsLang(): void
    {
        $category = Category::makeFactory()->create();

        $category->langs()->delete();

        /** @var ExistsLangRule */
        $rule = App::make(ExistsLangRule::class, [
            'table' => $category->getTable(),
            'column' => 'id'
        ]);

        $this->assertFalse($rule->passes('id', $category->id));
    }

    public function testNoExistsWithAdditionalQuery(): void
    {
        $category = Category::makeFactory()->inactive()->create();

        /** @var ExistsLangRule */
        $rule = App::make(ExistsLangRule::class, [
            'table' => $category->getTable(),
            'column' => 'id',
            'query' => function (Builder $query) use ($category) {
                return $query->where("{$category->getTable()}.status", Status::ACTIVE);
            }
        ]);

        $this->assertFalse($rule->passes('id', $category->id));
    }

    public function testExists(): void
    {
        $category = Category::makeFactory()->create();

        /** @var ExistsLangRule */
        $rule = App::make(ExistsLangRule::class, [
            'table' => $category->getTable(),
            'column' => 'id'
        ]);

        $this->assertTrue($rule->passes('id', $category->id));
    }

    public function testExistsWithAdditionalQuery(): void
    {
        $category = Category::makeFactory()->active()->create();

        /** @var ExistsLangRule */
        $rule = App::make(ExistsLangRule::class, [
            'table' => $category->getTable(),
            'column' => 'id',
            'query' => function (Builder $query) use ($category) {
                return $query->where("{$category->getTable()}.status", Status::ACTIVE);
            }
        ]);

        $this->assertTrue($rule->passes('id', $category->id));
    }
}
