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

namespace N1ebieski\ICore\Models\Traits;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Laravel\Sanctum\NewAccessToken;
use Laravel\Sanctum\HasApiTokens as BaseHasApiTokens;
use N1ebieski\ICore\Models\Token\PersonalAccessToken;

trait HasApiTokens
{
    use BaseHasApiTokens;

    /**
     * Undocumented function
     *
     * @param string $name
     * @param array $abilities
     * @param int|null $expireMinutes
     * @return NewAccessToken
     */
    public function createToken(string $name, array $abilities = ['*'], int $expireMinutes = null)
    {
        /** @var PersonalAccessToken */
        $token = $this->tokens()->create([
            'name' => $name,
            'token' => hash('sha256', $plainTextToken = Str::random(40)),
            'abilities' => $abilities,
            'expired_at' => $expireMinutes ? Carbon::now()->addMinutes($expireMinutes) : null
        ]);

        return new NewAccessToken($token, $token->getKey() . '|' . $plainTextToken);
    }
}
