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

namespace N1ebieski\ICore\Tests\Feature\Web\Socialite;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use N1ebieski\ICore\Testing\Socialite\Traits\HasSocialite;

class RedirectSocialiteTest extends TestCase
{
    use DatabaseTransactions;
    use HasSocialite;

    /**
     *
     * @var array
     */
    private array $socialLoginRedirects = [
        'facebook' => 'https://www.facebook.com/v3.3/dialog/oauth',
        'google'   => 'https://accounts.google.com/o/oauth2/auth',
        'github'   => 'https://github.com/login/oauth/authorize',
        'twitter'  => 'https://api.twitter.com/oauth/authenticate'
    ];

    /**
     * @dataProvider providerProvider
     */
    public function testRedirectProvider(string $provider): void
    {
        // Check that the user is redirected to the Social Platform Login Page
        $loginResponse = $this->get(route('auth.socialite.redirect', ['provider' => $provider]));

        $loginResponse->assertStatus(302);

        /** @var string */
        $redirectLocation = $loginResponse->headers->get('Location');

        $this->assertStringContainsString(
            $this->socialLoginRedirects[$provider],
            $redirectLocation,
            sprintf(
                'The Social Login Redirect does not match the expected value for the provider %s. Expected to contain %s but got %s',
                $provider,
                $this->socialLoginRedirects[$provider],
                $redirectLocation
            )
        );
    }
}
