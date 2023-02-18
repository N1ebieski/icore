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

namespace N1ebieski\ICore\Tests\Integration\Crons\AutoTranslate;

use Closure;
use Eloquent;
use Tests\TestCase;
use Mockery\MockInterface;
use N1ebieski\ICore\Models\Post;
use Illuminate\Support\Facades\App;
use N1ebieski\ICore\Models\Mailing;
use N1ebieski\ICore\Models\Page\Page;
use Illuminate\Support\Facades\Config;
use N1ebieski\ICore\ValueObjects\Lang;
use N1ebieski\ICore\Models\PageLang\PageLang;
use N1ebieski\ICore\Models\PostLang\PostLang;
use Google\Cloud\Translate\V2\TranslateClient;
use N1ebieski\ICore\Models\Category\Post\Category;
use N1ebieski\ICore\Models\MailingLang\MailingLang;
use N1ebieski\ICore\Models\Tag\Page\Tag as PageTag;
use N1ebieski\ICore\Models\Tag\Post\Tag as PostTag;
use N1ebieski\ICore\Models\CategoryLang\CategoryLang;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use N1ebieski\ICore\Crons\AutoTranslate\AutoTranslateCron;
use N1ebieski\ICore\Models\Interfaces\AutoTranslateInterface;
use Illuminate\Contracts\Container\BindingResolutionException;

class AutoTranslateCronTest extends TestCase
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

        App::setLocale('pl');

        Config::set('icore.auto_translate.check_days', 1);
        Config::set('icore.multi_langs', ['pl', 'en']);
    }

    /**
     *
     * @param array $response
     * @return TranslateClient
     */
    protected function createTranslateClientStub(array $response): TranslateClient
    {
        /** @var TranslateClient */
        $translateClient = $this->mock(TranslateClient::class, function (MockInterface $mock) use ($response) {
            $mock->shouldReceive('translateBatch')->andReturn($response);
        });

        return $translateClient;
    }

    /**
     *
     * @return array
     */
    protected function cronProvider(): array
    {
        return [
            [
                function (): Category {
                    /** @var Category */
                    $category = Category::makeFactory()->withoutLangs()->autoTrans()->create();

                    CategoryLang::makeFactory()->for($category)->create([
                        'progress' => 100,
                        'lang' => 'pl'
                    ]);

                    return $category;
                },
                [
                    [
                        "source" => "pl",
                        "input" => "Nazwa kategorii",
                        "text" => "Name of category",
                        "model" => null
                    ]
                ]
            ],
            [
                function () {
                    /** @var Page */
                    $page = Page::makeFactory()->withoutLangs()->autoTrans()->create();

                    PageLang::makeFactory()->for($page)->create([
                        'progress' => 100,
                        'lang' => 'pl'
                    ]);

                    PageTag::makeFactory()->hasAttached($page, [], 'morphs')->count(5)->create();

                    return $page;
                },
                [
                    [
                        "source" => "pl",
                        "input" => "Tytuł podstrony",
                        "text" => "Title of page",
                        "model" => null
                    ],
                    [
                        "source" => "pl",
                        "input" => "Treść podstrony",
                        "text" => "Content of page",
                        "model" => null
                    ],
                    [
                        "source" => "pl",
                        "input" => "",
                        "text" => "",
                        "model" => null
                    ],
                    [
                        "source" => "pl",
                        "input" => "",
                        "text" => "",
                        "model" => null
                    ],
                    [
                        "source" => "pl",
                        "input" => "tag1, tag2, tag3, tag4, tag5",
                        "text" => "tag in english 1, tag in english 2, tag in english 3, tag in english 4, tag in english 5",
                        "model" => null
                    ]
                ]
            ],
            [
                function (): Post {
                    /** @var Post */
                    $post = Post::makeFactory()->withoutLangs()->autoTrans()->active()->publish()->create();

                    PostLang::makeFactory()->for($post)->create([
                        'progress' => 100,
                        'lang' => 'pl'
                    ]);

                    PostTag::makeFactory()->hasAttached($post, [], 'morphs')->count(5)->create();

                    return $post;
                },
                [
                    [
                        "source" => "pl",
                        "input" => "Tytuł posta",
                        "text" => "Title of post",
                        "model" => null
                    ],
                    [
                        "source" => "pl",
                        "input" => "Treść posta",
                        "text" => "Content of post",
                        "model" => null
                    ],
                    [
                        "source" => "pl",
                        "input" => "",
                        "text" => "",
                        "model" => null
                    ],
                    [
                        "source" => "pl",
                        "input" => "",
                        "text" => "",
                        "model" => null
                    ],
                    [
                        "source" => "pl",
                        "input" => "tag1, tag2, tag3, tag4, tag5",
                        "text" => "tag in english 1, tag in english 2, tag in english 3, tag in english 4, tag in english 5",
                        "model" => null
                    ]
                ]
            ],
            [
                function () {
                    /** @var Mailing */
                    $mailing = Mailing::makeFactory()->withoutLangs()->autoTrans()->create();

                    MailingLang::makeFactory()->for($mailing)->create([
                        'progress' => 100,
                        'lang' => 'pl'
                    ]);

                    return $mailing;
                },
                [
                    [
                        "source" => "pl",
                        "input" => "Tytuł mailing",
                        "text" => "Title of mailing",
                        "model" => null
                    ],
                    [
                        "source" => "pl",
                        "input" => "Treść mailingu",
                        "text" => "Content of mailing",
                        "model" => null
                    ]
                ]
            ],
        ];
    }

    /**
     * @dataProvider cronProvider
     */
    public function testCron(Closure $seed, array $response): void
    {
        /** @var Eloquent&AutoTranslateInterface */
        $model = $seed();

        $this->assertNull($model->langs->firstWhere('lang', new Lang('en')));

        $this->createTranslateClientStub($response);

        $schedule = App::make(AutoTranslateCron::class);
        $schedule();

        $model->load('langs');

        $this->assertTrue($model->langs->firstWhere('lang', new Lang('en'))?->exists());

        if (method_exists($model, 'tags')) {
            // @phpstan-ignore-next-line
            $this->assertCount(5, $model->tags->whereIn('lang', new Lang('en')));
        }
    }
}
