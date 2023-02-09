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
use N1ebieski\ICore\Models\Post;
use Illuminate\Support\Facades\Config;
use Illuminate\Database\Eloquent\Collection;
use N1ebieski\ICore\Models\PostLang\PostLang;
use N1ebieski\ICore\Models\Category\Post\Category;
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

    public function testChunkActiveWithModelsCount(): void
    {
        /** @var Category */
        $category = Category::makeFactory()->active()->create();

        foreach (['pl' => 1, 'en' => 2] as $lang => $count) {
            /** @var array<Collection<Post>> */
            $posts[$lang] = Post::makeFactory()->active()->publish()->count($count)->hasAttached($category)->withoutLangs()->create();

            foreach ($posts[$lang] as $post) {
                PostLang::makeFactory()->for($post)->create([
                    'lang' => $lang
                ]);
            }
        }

        $category->makeRepo()->chunkActiveWithModelsCount(1, function (Collection $collection) use ($posts) {
            $collection->each(function (Category $category) use ($posts) {
                foreach ($category->langs as $categoryLang) {
                    $key = "models_count_{$categoryLang->lang->getValue()}";

                    $this->assertEquals(count($posts[$categoryLang->lang->getValue()]), $category->{$key});
                }
            });
        });
    }
}
