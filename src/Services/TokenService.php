<?php

namespace N1ebieski\ICore\Services;

use Illuminate\Contracts\Auth\Guard as Auth;
use N1ebieski\ICore\Services\Interfaces\Deletable;
use Illuminate\Contracts\Config\Repository as Config;
use N1ebieski\ICore\Models\PersonalAccessToken as Token;

class TokenService implements Deletable
{
    /**
     * Undocumented variable
     *
     * @var Token
     */
    protected $token;

    /**
     * Undocumented variable
     *
     * @var Auth
     */
    protected $auth;

    /**
     * Undocumented variable
     *
     * @var Config
     */
    protected $config;

    /**
     * Undocumented function
     *
     * @param Token $token
     * @param Auth $auth
     * @param Config $config
     */
    public function __construct(Token $token, Auth $auth, Config $config)
    {
        $this->token = $token;

        $this->auth = $auth;
        $this->config = $config;
    }

    /**
     * Set the value of token
     *
     * @return  self
     */
    public function setToken(Token $token)
    {
        $this->token = $token;

        return $this;
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

        $accessToken = $user->createToken($attributes['name'], $attributes['scopes'], $attributes['expiration']);

        if (array_key_exists('refresh', $attributes) && $attributes['refresh'] === true) {
            $refreshToken = $user->createToken($attributes['name'], ['refresh'], $this->config->get('sanctum.refresh_expiration'));

            $accessToken->accessToken->symlink()->associate($refreshToken->accessToken)->save();
            $refreshToken->accessToken->symlink()->associate($accessToken->accessToken)->save();
        }

        return [$accessToken, $refreshToken ?? null];
    }

    /**
     * [delete description]
     * @return bool [description]
     */
    public function delete(): bool
    {
        $this->token->symlink()->delete();

        return $this->token->delete();
    }
}
