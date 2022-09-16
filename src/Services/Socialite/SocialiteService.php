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

namespace N1ebieski\ICore\Services\Socialite;

use Throwable;
use N1ebieski\ICore\Models\User;
use Illuminate\Database\DatabaseManager as DB;
use N1ebieski\ICore\Models\Socialite as Social;
use Laravel\Socialite\Contracts\User as ProviderUser;

class SocialiteService
{
    /**
     * Zwraca dane usera z Socialite
     * @var ProviderUser
     */
    protected $providerUser;

    /**
     * Nazwa providera np "facebook"
     * @var string
     */
    protected $provider;

    /**
     * Zautentykowany user
     * @var User|null
     */
    protected $socialiteUser;

    /**
     * Undocumented function
     *
     * @param Social $socialite
     * @param DB $db
     */
    public function __construct(
        protected Social $socialite,
        protected DB $db
    ) {
        //
    }

    /**
     * @param  ProviderUser $providerUser
     * @param  string $provider
     * @return User
     */
    public function findOrCreateUser(ProviderUser $providerUser, string $provider): User
    {
        return $this->db->transaction(function () use ($providerUser, $provider) {
            if (is_null($this->findUser($providerUser, $provider))) {
                $this->socialiteUser = $this->createUser();
            }

            return $this->socialiteUser;
        });
    }

    /**
     * @param  ProviderUser $providerUser
     * @param  string $provider
     * @return User|null
     */
    public function findUser(ProviderUser $providerUser, string $provider): ?User
    {
        $this->providerUser = $providerUser;
        $this->provider = $provider;

        // Sprawdzenie czy uzytkownik jest zarejestrowany jako Socialite
        $socialite = $this->socialite->makeRepo()->firstByProvider(
            $this->provider,
            $this->providerUser->getId()
        );

        return $socialite instanceof Social ?
            $this->socialiteUser = $socialite->user : null;
    }

    /**
     * @return User
     */
    public function createUser(): User
    {
        // Sprawdzenie czy provider zwraca adres e-mail
        if (empty($this->providerUser->getEmail())) {
            throw new \N1ebieski\ICore\Exceptions\Socialite\NoEmailException();
        }

        // Sprawdzenie czy uzytkownik jest zarejestrowany jako User
        $this->socialiteUser = $this->socialite->user()->make()
            ->makeRepo()->firstByEmail($this->providerUser->getEmail());

        // Jesli tak, odrzucamy request i prosimy usera by powiazal konto z poziomu edycji profilu
        if ($this->socialiteUser) {
            throw new \N1ebieski\ICore\Exceptions\Socialite\EmailExistException();
        }

        // Jesli nie, tworzymy go
        return $this->db->transaction(function () {
            /** @var User */
            $this->socialiteUser = $this->socialite->user()->create([
                'name' => str_replace(' ', '_', $this->providerUser->getName()),
                'email' => $this->providerUser->getEmail(),
            ]);

            $this->socialiteUser->assignRole('user');
            $this->socialiteUser->sendEmailVerificationNotification();

            // Tworzymy mu jeszcze powiazanie z Socialite
            $this->create([
                'provider_id'   => $this->providerUser->getId(),
                'provider_name' => $this->provider,
                'user' => $this->socialiteUser
            ]);

            return $this->socialiteUser;
        });
    }

    /**
     *
     * @param array $attributes
     * @return Social
     * @throws Throwable
     */
    public function create(array $attributes): Social
    {
        return $this->db->transaction(function () use ($attributes) {
            $this->socialite->fill($attributes);

            $this->socialite->user()->associate($attributes['user']);

            $this->socialite->save();

            return $this->socialite;
        });
    }
}
