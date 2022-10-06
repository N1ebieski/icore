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

namespace N1ebieski\ICore\Services\Token;

use Illuminate\Contracts\Auth\Guard as Auth;
use Illuminate\Contracts\Config\Repository as Config;
use N1ebieski\ICore\Models\Token\PersonalAccessToken;
use N1ebieski\ICore\Models\Token\PersonalAccessToken as Token;

class TokenService
{
    /**
     * Undocumented function
     *
     * @param Token $token
     * @param Auth $auth
     * @param Config $config
     */
    public function __construct(
        protected Token $token,
        protected Auth $auth,
        protected Config $config
    ) {
        //
    }

    /**
     * Undocumented function
     *
     * @param array $attributes
     * @return array
     */
    public function create(array $attributes): array
    {
        /**
         * @var \N1ebieski\ICore\Models\User
         */
        $user = $this->token->tokenable ?? $this->auth->user();

        $newAccessToken = $user->createToken($attributes['name'], $attributes['abilities'], $attributes['expiration']);

        if (array_key_exists('refresh', $attributes) && $attributes['refresh'] === true) {
            $newRefreshToken = $user->createToken($attributes['name'], ['refresh'], $this->config->get('sanctum.refresh_expiration'));

            /** @var PersonalAccessToken */
            $accessToken = $newAccessToken->accessToken;

            /** @var PersonalAccessToken */
            $refreshToken = $newRefreshToken->accessToken;

            $accessToken->symlink()->associate($refreshToken)->save();
            $refreshToken->symlink()->associate($accessToken)->save();
        }

        return [$newAccessToken, $newRefreshToken ?? null];
    }

    /**
     * [delete description]
     * @return bool|null [description]
     */
    public function delete(): ?bool
    {
        $this->token->symlink()->delete();

        return $this->token->delete();
    }
}
