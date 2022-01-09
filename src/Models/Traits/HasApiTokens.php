<?php

namespace N1ebieski\ICore\Models\Traits;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Laravel\Sanctum\NewAccessToken;
use Laravel\Sanctum\HasApiTokens as BaseHasApiTokens;

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
        $token = $this->tokens()->create([
            'name' => $name,
            'token' => hash('sha256', $plainTextToken = Str::random(40)),
            'abilities' => $abilities,
            'expired_at' => $expireMinutes ? Carbon::now()->addMinutes($expireMinutes) : null
        ]);

        return new NewAccessToken($token, $token->getKey() . '|' . $plainTextToken);
    }
}
