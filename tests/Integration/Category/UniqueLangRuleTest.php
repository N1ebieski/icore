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
use Illuminate\Support\Facades\App;
use N1ebieski\ICore\Rules\UniqueLangRule;
use N1ebieski\ICore\Models\Category\Category;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UniqueLangRuleTest extends TestCase
{
    use DatabaseTransactions;

    public function testNoUnique(): void
    {
        $category = Category::makeFactory()->create();

        /** @var UniqueLangRule */
        $rule = App::make(UniqueLangRule::class, [
            'table' => $category->getTable(),
            'column' => 'name'
        ]);

        $this->assertFalse($rule->passes('name', $category->name));
    }

    public function testNoUniqueWithAdditionalQuery(): void
    {
        $parent = Category::makeFactory()->create();

        $category = Category::makeFactory()->for($parent, 'parent')->create();

        /** @var UniqueLangRule */
        $rule = App::make(UniqueLangRule::class, [
            'table' => $category->getTable(),
            'column' => 'name',
            'query' => function (Builder $query) use ($category) {
                return $query->where("{$category->getTable()}.parent_id", $category->parent_id);
            }
        ]);

        $this->assertFalse($rule->passes('name', $category->name));
    }

    public function testUnique(): void
    {
        $category = Category::makeFactory()->create();

        /** @var UniqueLangRule */
        $rule = App::make(UniqueLangRule::class, [
            'table' => $category->getTable(),
            'column' => 'name'
        ]);

        $this->assertTrue($rule->passes('name', 'Xxxxxxxxxxxxxxxxx'));
    }

    public function testUniqueWithIgnore(): void
    {
        $category = Category::makeFactory()->create();

        /** @var UniqueLangRule */
        $rule = App::make(UniqueLangRule::class, [
            'table' => $category->getTable(),
            'column' => 'name',
            'ignore' => $category->id
        ]);

        $this->assertTrue($rule->passes('name', $category->name));
    }

    public function testUniqueWithAdditionalQuery(): void
    {
        $parent = Category::makeFactory()->create();

        $category = Category::makeFactory()->for($parent, 'parent')->create();

        /** @var UniqueLangRule */
        $rule = App::make(UniqueLangRule::class, [
            'table' => $category->getTable(),
            'column' => 'name',
            'query' => function (Builder $query) use ($category) {
                return $query->whereNull("{$category->getTable()}.parent_id");
            }
        ]);

        $this->assertTrue($rule->passes('name', $category->name));
    }
}
