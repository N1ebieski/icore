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

namespace N1ebieski\ICore\Tests\Integration\Post;

use Tests\TestCase;
use N1ebieski\ICore\Models\Post;
use Illuminate\Support\Facades\Config;
use Illuminate\Database\Eloquent\Collection;
use N1ebieski\ICore\Models\PostLang\PostLang;
use N1ebieski\ICore\Models\Comment\Post\Comment;
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
        /** @var Post */
        $post = Post::makeFactory()->active()->commentable()->publish()->withUser()->withoutLangs()->create();

        foreach (['pl' => 1, 'en' => 2] as $lang => $count) {
            PostLang::makeFactory()->for($post)->create([
                'lang' => $lang
            ]);

            /** @var array<array<Comment>> */
            $comments[$lang] = Comment::makeFactory()->active()->count($count)->for($post, 'morph')->create([
                'lang' => $lang
            ]);
        }

        $post->makeRepo()->chunkActiveWithModelsCount(1, function (Collection $collection) use ($comments) {
            $collection->each(function (Post $post) use ($comments) {
                foreach ($post->langs as $postLang) {
                    $key = "models_count_{$postLang->lang->getValue()}";

                    $this->assertEquals(count($comments[$postLang->lang->getValue()]), $post->{$key});
                }
            });
        });
    }
}
