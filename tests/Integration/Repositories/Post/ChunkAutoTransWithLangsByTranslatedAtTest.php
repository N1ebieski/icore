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

namespace N1ebieski\ICore\Tests\Integration\Repositories\Post;

use Tests\TestCase;
use Illuminate\Support\Carbon;
use N1ebieski\ICore\Models\Post;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Database\Eloquent\Collection;
use N1ebieski\ICore\Models\PostLang\PostLang;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Contracts\Container\BindingResolutionException;

class ChunkAutoTransWithLangsByTranslatedAtTest extends TestCase
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

        Config::set('icore.auto_translate.check_days', null);
        Config::set('icore.multi_langs', ['pl', 'en', 'de']);
    }

    public function testIfHasAllLangs(): void
    {
        /** @var Post */
        $post = Post::makeFactory()->active()->publish()->withoutLangs()->autoTrans()->create();

        foreach (['pl', 'en', 'de'] as $lang) {
            PostLang::makeFactory()->for($post)->create([
                'lang' => $lang,
                'progress' => 100
            ]);
        }

        $post->makeRepo()->chunkAutoTransWithLangsByTranslatedAt(function (Collection $collection) {
            $this->assertEmpty($collection);
        });

        $this->assertTrue(true);
    }

    public function testIfDoesntHaveAnyLangWithProgress100(): void
    {
        /** @var Post */
        $post = Post::makeFactory()->active()->publish()->withoutLangs()->autoTrans()->create();

        foreach (['pl', 'en'] as $lang) {
            PostLang::makeFactory()->for($post)->create([
                'lang' => $lang,
                'progress' => rand(1, 99)
            ]);
        }

        $post->makeRepo()->chunkAutoTransWithLangsByTranslatedAt(function (Collection $collection) {
            $this->assertEmpty($collection);
        });

        $this->assertTrue(true);
    }

    public function testIfDoesntHaveOneLang(): void
    {
        /** @var Post */
        $post = Post::makeFactory()->active()->publish()->withoutLangs()->autoTrans()->create();

        foreach (['pl', 'en'] as $lang) {
            PostLang::makeFactory()->for($post)->create([
                'lang' => $lang,
                'progress' => 100
            ]);
        }

        $post->makeRepo()->chunkAutoTransWithLangsByTranslatedAt(function (Collection $collection) use ($post) {
            $this->assertEquals($post->getKey(), $collection->first()->getKey());
        });
    }

    public function testIfHasAllLangsWithProgressGreaterThan0WithOldTranslatedAt(): void
    {
        $days = 7;

        Config::set('icore.auto_translate.check_days', $days);

        /** @var Post */
        $post = Post::makeFactory()->active()->publish()->withoutLangs()->autoTrans()->create();

        PostLang::makeFactory()->for($post)->create([
            'lang' => 'pl',
            'progress' => 100
        ]);

        foreach (['en', 'de'] as $lang) {
            PostLang::makeFactory()->for($post)->create([
                'lang' => $lang,
                'progress' => rand(1, 99),
                'translated_at' => Carbon::now()->subDays($days)
            ]);
        }

        $post->makeRepo()->chunkAutoTransWithLangsByTranslatedAt(function (Collection $collection) use ($post) {
            $this->assertEquals($post->getKey(), $collection->first()->getKey());
        }, Carbon::now()->subDays($days));

        $this->assertTrue(true);
    }

    public function testIfHasAllLangsButOneHasNullTranslatedAt(): void
    {
        $days = 7;

        Config::set('icore.auto_translate.check_days', $days);

        /** @var Post */
        $post = Post::makeFactory()->active()->publish()->withoutLangs()->autoTrans()->create();

        foreach (['pl', 'en'] as $lang) {
            PostLang::makeFactory()->for($post)->create([
                'lang' => $lang,
                'progress' => 100
            ]);
        }

        PostLang::makeFactory()->for($post)->create([
            'lang' => 'de',
            'progress' => 0,
            'translated_at' => null
        ]);

        $post->makeRepo()->chunkAutoTransWithLangsByTranslatedAt(function (Collection $collection) use ($post) {
            $this->assertEquals($post->getKey(), $collection->first()->getKey());
        }, Carbon::now()->subDays($days));
    }

    public function testIfHasAllLangsButOneHasOldTranslatedAt(): void
    {
        $days = 7;

        Config::set('icore.auto_translate.check_days', $days);

        /** @var Post */
        $post = Post::makeFactory()->active()->publish()->withoutLangs()->autoTrans()->create();

        foreach (['pl', 'en'] as $lang) {
            PostLang::makeFactory()->for($post)->create([
                'lang' => $lang,
                'progress' => 100
            ]);
        }

        PostLang::makeFactory()->for($post)->create([
            'lang' => 'de',
            'progress' => 0,
            'translated_at' => Carbon::now()->subDays($days)
        ]);

        $post->makeRepo()->chunkAutoTransWithLangsByTranslatedAt(function (Collection $collection) use ($post) {
            $this->assertEquals($post->getKey(), $collection->first()->getKey());
        }, Carbon::now()->subDays($days));
    }
}
