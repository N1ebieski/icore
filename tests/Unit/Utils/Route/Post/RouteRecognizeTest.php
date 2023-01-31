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

namespace N1ebieski\ICore\Tests\Unit\Utils\Route\Post;

use Illuminate\Routing\Route;
use PHPUnit\Framework\TestCase;
use N1ebieski\ICore\Models\Post;
use PHPUnit\Framework\Exception;
use Illuminate\Support\Collection as Collect;
use N1ebieski\ICore\Models\PostLang\PostLang;
use Illuminate\Contracts\Routing\UrlGenerator;
use N1ebieski\ICore\Utils\Route\RouteRecognize;
use PHPUnit\Framework\InvalidArgumentException;
use PHPUnit\Framework\MockObject\RuntimeException;
use PHPUnit\Framework\MockObject\ReflectionException;
use PHPUnit\Framework\MockObject\UnknownTypeException;
use PHPUnit\Framework\MockObject\ClassIsFinalException;
use PHPUnit\Framework\MockObject\ClassIsReadonlyException;
use PHPUnit\Framework\MockObject\DuplicateMethodException;
use PHPUnit\Framework\MockObject\InvalidMethodNameException;
use PHPUnit\Framework\MockObject\ClassAlreadyExistsException;
use PHPUnit\Framework\MockObject\OriginalConstructorInvocationRequiredException;

class RouteRecognizeTest extends TestCase
{
    public function testSlugIfLangExists(): void
    {
        $post = $this->createPostStub();

        $route = $this->createRouteWithSlugStub($post);

        $url = $this->createMock(UrlGenerator::class);

        $url->expects($this->once())
            ->method('route')
            ->with(
                $this->equalTo('web.post.show'),
                $this->equalTo([
                    'lang' => 'en',
                    // @phpstan-ignore-next-line
                    'post_cache' => $post->langs[1]->slug
                ])
            )
            ->willReturn('string');

        $routeRecognizer = new RouteRecognize($route, $url, new Collect());

        $routeRecognizer->getCurrentUrlWithLang('en');
    }

    /**
     *
     * @return array
     */
    private function idProvider(): array
    {
        $post = $this->createPostStub();

        return [
            [
                (clone $post)->setAttribute('id', '1')
            ],
            [
                (clone $post)->setRawAttributes([
                    // @phpstan-ignore-next-line
                    'slug' => $post->langs[0]->slug,
                    'id' => '1',
                ])
            ]
        ];
    }

    /**
     * @dataProvider idProvider
     */
    public function testIdIfLangExists(Post $post): void
    {
        $route = $this->createRouteWithIdStub($post);

        $url = $this->createMock(UrlGenerator::class);

        $url->expects($this->once())
            ->method('route')
            ->with(
                $this->equalTo('web.post.show'),
                $this->equalTo([
                    'lang' => 'en',
                    'post' => $post->id
                ])
            )
            ->willReturn('string');

        $routeRecognizer = new RouteRecognize($route, $url, new Collect());

        $routeRecognizer->getCurrentUrlWithLang('en');
    }

    /**
     *
     * @return array
     */
    private function uuidProvider(): array
    {
        $post = $this->createPostStub();

        return [
            [
                (clone $post)->setAttribute('uuid', 'f0a36099-180c-4df0-a990-f988f76bd2fe')
            ],
            [
                (clone $post)->setRawAttributes([
                    // @phpstan-ignore-next-line
                    'slug' => $post->langs[0]->slug,
                    'uuid' => 'f0a36099-180c-4df0-a990-f988f76bd2fe'
                ])
            ],
            [
                (clone $post)->setRawAttributes([
                    // @phpstan-ignore-next-line
                    'slug' => $post->langs[0]->slug,
                    'id' => '1',
                    'uuid' => 'f0a36099-180c-4df0-a990-f988f76bd2fe'
                ])
            ]
        ];
    }

    /**
     * @dataProvider uuidProvider
     */
    public function testUuidIfLangExists(Post $post): void
    {
        $route = $this->createRouteWithUuidStub($post);

        $url = $this->createMock(UrlGenerator::class);

        $url->expects($this->once())
            ->method('route')
            ->with(
                $this->equalTo('web.post.show'),
                $this->equalTo([
                    'lang' => 'en',
                    // @phpstan-ignore-next-line
                    'post' => $post->uuid
                ])
            )
            ->willReturn('string');

        $routeRecognizer = new RouteRecognize($route, $url, new Collect());

        $routeRecognizer->getCurrentUrlWithLang('en');
    }

    public function testSlugIfLangDoesntExist(): void
    {
        $post = $this->createPostStub(['pl']);

        $route = $this->createRouteWithSlugStub($post);

        $url = $this->createStub(UrlGenerator::class);

        $routeRecognizer = new RouteRecognize($route, $url, new Collect());

        $this->assertFalse($routeRecognizer->getCurrentUrlWithLang('en'));
    }

    /**
     *
     * @param array $langs
     * @return Post
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws ClassAlreadyExistsException
     * @throws ClassIsFinalException
     * @throws ClassIsReadonlyException
     * @throws DuplicateMethodException
     * @throws InvalidMethodNameException
     * @throws OriginalConstructorInvocationRequiredException
     * @throws ReflectionException
     * @throws RuntimeException
     * @throws UnknownTypeException
     */
    private function createPostStub(array $langs = ['pl', 'en']): Post
    {
        $post = $this->createPartialMock(Post::class, ['__construct']);

        $postLangs = new Collect();

        foreach ($langs as $lang) {
            $postLang = $this->createPartialMock(PostLang::class, ['__construct']);

            $postLang->setRawAttributes([
                'slug' => "{$lang}-slug-1",
                'lang' => $lang
            ]);

            $postLangs->push($postLang);
        }

        $post->setRelation('langs', $postLangs);

        return $post;
    }

    /**
     *
     * @param Post $post
     * @return Route
     * @throws InvalidArgumentException
     * @throws ClassAlreadyExistsException
     * @throws ClassIsFinalException
     * @throws ClassIsReadonlyException
     * @throws DuplicateMethodException
     * @throws InvalidMethodNameException
     * @throws OriginalConstructorInvocationRequiredException
     * @throws ReflectionException
     * @throws RuntimeException
     * @throws UnknownTypeException
     */
    private function createRouteWithSlugStub(Post $post): Route
    {
        $route = $this->createStub(Route::class);

        $route->method('getName')->willReturn('web.post.show');
        $route->method('originalParameters')->willReturn([
            'lang' => 'pl',
            // @phpstan-ignore-next-line
            'post_cache' => $post->langs[0]->slug
        ]);
        $route->method('parameters')->willReturn([
            'post_cache' => $post
        ]);
        $route->wheres = [
            'lang' => '(pl|en)',
            'post_cache' => "[0-9A-Za-z,_-]+"
        ];

        return $route;
    }

    /**
     *
     * @param Post $post
     * @return Route
     * @throws InvalidArgumentException
     * @throws ClassAlreadyExistsException
     * @throws ClassIsFinalException
     * @throws ClassIsReadonlyException
     * @throws DuplicateMethodException
     * @throws InvalidMethodNameException
     * @throws OriginalConstructorInvocationRequiredException
     * @throws ReflectionException
     * @throws RuntimeException
     * @throws UnknownTypeException
     */
    private function createRouteWithIdStub(Post $post): Route
    {
        $route = $this->createStub(Route::class);

        $route->method('getName')->willReturn('web.post.show');
        $route->method('originalParameters')->willReturn([
            'lang' => 'pl',
            'post' => $post->id
        ]);
        $route->method('parameters')->willReturn([
            'post' => $post
        ]);
        $route->wheres = [
            'lang' => '(pl|en)',
            'post' => "[0-9]+"
        ];

        return $route;
    }

    private function createRouteWithUuidStub(Post $post): Route
    {
        $route = $this->createStub(Route::class);

        $route->method('getName')->willReturn('web.post.show');
        $route->method('originalParameters')->willReturn([
            'lang' => 'pl',
            // @phpstan-ignore-next-line
            'post' => $post->uuid
        ]);
        $route->method('parameters')->willReturn([
            'post' => $post
        ]);
        $route->wheres = [
            'lang' => '(pl|en)',
            'post' =>  "[0-9A-Za-z,_-]+"
        ];

        return $route;
    }
}
