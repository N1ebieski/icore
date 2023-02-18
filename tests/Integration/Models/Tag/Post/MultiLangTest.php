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

namespace N1ebieski\ICore\Tests\Integration\Models\Tag\Post;

use Tests\TestCase;
use N1ebieski\ICore\Models\Post;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use N1ebieski\ICore\Models\Tag\Post\Tag;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use N1ebieski\ICore\Database\Factories\Post\PostFactory;
use Illuminate\Contracts\Container\BindingResolutionException;

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

    public function testTagList(): void
    {
        /** @var PostFactory */
        $postFactory = Post::makeFactory()->active()->publish()->withUser();

        foreach (['pl', 'en'] as $lang) {
            /** @var array<string, Collection<Tag>> $tags */
            $tags[$lang] = Tag::makeFactory()->count(rand(1, 5))->create([
                'lang' => $lang
            ]);

            $postFactory = $postFactory->hasAttached($tags[$lang]);
        }

        App::setLocale('pl');

        /** @var Post */
        $post = $postFactory->create();
        $post->loadAllRels();

        $this->assertTrue($tags['pl']->pluck('name')->implode(', ') === $post->tag_list);
        $this->assertFalse($tags['en']->pluck('name')->implode(', ') === $post->tag_list);
    }

    public function testWithAnyTagsScope(): void
    {
        /** @var PostFactory */
        $postFactory = Post::makeFactory()->active()->publish()->withUser();

        foreach (['pl', 'en'] as $lang) {
            /** @var array<string, Tag> $tags */
            $tags[$lang] = Tag::makeFactory()->create([
                'name' => 'same name but different lang',
                'lang' => $lang
            ]);

            $postFactory = $postFactory->hasAttached($tags[$lang]);
        }

        App::setLocale('pl');

        /** @var Post */
        $post = $postFactory->create();

        $query = $post->withAnyTags('same name but different lang');

        $this->assertTrue(in_array($tags['pl']->getKey(), $query->getQuery()->wheres[0]['values']));
        $this->assertFalse(in_array($tags['en']->getKey(), $query->getQuery()->wheres[0]['values']));
    }

    public function testFindByName(): void
    {
        /** @var PostFactory */
        $postFactory = Post::makeFactory()->active()->publish()->withUser();

        foreach (['pl', 'en'] as $lang) {
            /** @var array<string, Tag> $tags */
            $tags[$lang] = Tag::makeFactory()->create([
                'name' => 'same name but different lang',
                'lang' => $lang
            ]);

            $postFactory = $postFactory->hasAttached($tags[$lang]);
        }

        App::setLocale('pl');

        $postFactory->create();

        /** @var Tag */
        $tag = Tag::findByName('same name but different lang');

        $this->assertTrue($tags['pl']->getKey() === $tag->getKey());
        $this->assertFalse($tags['en']->getKey() === $tag->getKey());
    }
}
