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

namespace N1ebieski\ICore\Tests\Integration\Sitemap;

use Closure;
use XMLReader;
use Tests\TestCase;
use N1ebieski\ICore\Models\Post;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\File;
use N1ebieski\ICore\Models\Page\Page;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Collection;
use N1ebieski\ICore\Crons\Sitemap\SitemapCron;
use N1ebieski\ICore\Crons\Sitemap\Builder\Builder;
use N1ebieski\ICore\Models\Category\Post\Category;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Contracts\Container\BindingResolutionException;

class SitemapTest extends TestCase
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

        Config::set('icore.sitemap.limit', 5);
        Config::set('icore.sitemap.max_items', 50);

        URL::defaults(['lang' => 'pl']);

        File::deleteDirectory(App::storagePath('app/public/vendor/icore/sitemap'));
    }

    /**
     *
     * @return array
     */
    protected function sitemapCronProvider(): array
    {
        return [
            [
                \N1ebieski\ICore\Crons\Sitemap\Builder\Category\Post\CategoryBuilder::class,
                function () {
                    return Category::makeFactory()->active()->count(100)->create();
                },
                function (Builder $builder, Category $category) {
                    return URL::route($builder->route, [$category->slug]);
                }
            ],
            [
                \N1ebieski\ICore\Crons\Sitemap\Builder\PageBuilder::class,
                function () {
                    return Page::makeFactory()->active()->count(100)->create();
                },
                function (Builder $builder, Page $page) {
                    return URL::route($builder->route, [$page->slug]);
                }
            ],
            [
                \N1ebieski\ICore\Crons\Sitemap\Builder\PostBuilder::class,
                function () {
                    return Post::makeFactory()->active()->publish()->count(100)->create();
                },
                function (Builder $builder, Post $post) {
                    return URL::route($builder->route, [$post->slug]);
                }
            ],
        ];
    }

    /**
     * @dataProvider sitemapCronProvider
     */
    public function testSitemapCron(string $namespace, Closure $seed, Closure $route): void
    {
        /** @var mixed */
        $modelBuilder = App::make($namespace);

        /** @var Collection */
        $models = $seed();

        $schedule = App::make(SitemapCron::class);
        $schedule();

        $modelSitemapPath = $modelBuilder->path . '/sitemap-2.xml';

        $this->assertFileExists(App::storagePath('app/public/' . $modelSitemapPath));

        $xml = new \XMLReader();

        $xml->open(App::storagePath('app/public/' . $modelSitemapPath));

        $xml->setParserProperty(XMLReader::VALIDATE, true);

        $this->assertTrue($xml->isValid());

        /** @var string */
        $modelSitemapContents = Storage::disk('public')->get($modelSitemapPath);

        $this->assertStringContainsString($route($modelBuilder, $models[50]), $modelSitemapContents);

        /** @var \N1ebieski\ICore\Crons\Sitemap\Builder\SitemapBuilder */
        $sitemapBuilder = App::make(\N1ebieski\ICore\Crons\Sitemap\Builder\SitemapBuilder::class);

        $sitemapPath = $sitemapBuilder->path . '/sitemap.xml';

        $this->assertFileExists(App::storagePath('app/public/' . $sitemapPath));

        $xml->open(App::storagePath('app/public/' . $sitemapPath));

        $xml->setParserProperty(XMLReader::VALIDATE, true);

        $this->assertTrue($xml->isValid());

        /** @var string */
        $sitemapContents = Storage::disk('public')->get($sitemapPath);

        $this->assertStringContainsString(URL::to('storage/' . $modelSitemapPath), $sitemapContents);
    }
}
