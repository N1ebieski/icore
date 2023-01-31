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

namespace N1ebieski\ICore\Testing\Socialite\Traits;

use Mockery;
use RuntimeException;
use Mockery\MockInterface;
use InvalidArgumentException;
use Laravel\Socialite\Facades\Socialite;

trait HasSocialite
{
    /**
     *
     * @return array
     */
    private function providerProvider(): array
    {
        return [['facebook'], ['twitter']];
    }

    /**
     *
     * @param array $user
     * @param string $provider
     * @return void
     * @throws RuntimeException
     * @throws InvalidArgumentException
     */
    private function createSocialiteMock(array $user, string $provider): void
    {
        /**
         * @var MockInterface
         */
        $abstractUser = Mockery::mock('Laravel\Socialite\Two\User');

        $abstractUser->shouldReceive('getId')->andReturn($user['id']);
        $abstractUser->shouldReceive('getEmail')->andReturn($user['email']);
        $abstractUser->shouldReceive('getName')->andReturn($user['name']);

        /**
         * @var MockInterface
         */
        $providerUser = Mockery::mock('Laravel\Socialite\Contracts\Provider');

        /** @phpstan-ignore-next-line */
        $providerUser->shouldReceive('user')->andReturn($abstractUser);

        Socialite::shouldReceive('driver')->once()->with($provider)->andReturn($providerUser);
    }
}
