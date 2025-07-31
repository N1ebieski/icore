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
use InvalidArgumentException;
use N1ebieski\ICore\Models\User;
use Illuminate\Database\DatabaseManager as DB;
use N1ebieski\ICore\Models\Socialite as Social;
use Laravel\Socialite\Contracts\User as ProviderUser;
use N1ebieski\ICore\Exceptions\Socialite\NoEmailException;
use N1ebieski\ICore\Exceptions\Socialite\EmailExistException;
use Illuminate\Contracts\Container\BindingResolutionException;

class SocialiteService
{
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
            $user = $this->findUser($providerUser, $provider);

            if (is_null($user)) {
                $user = $this->createUser($providerUser, $provider);
            }

            return $user;
        });
    }

    /**
     *
     * @param ProviderUser $providerUser
     * @param string $provider
     * @return null|User
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     */
    public function findUser(ProviderUser $providerUser, string $provider): ?User
    {
        // Sprawdzenie czy uzytkownik jest zarejestrowany jako Socialite
        $socialite = $this->socialite->makeRepo()->firstByProvider(
            $provider,
            $providerUser->getId()
        );

        return $socialite?->user;
    }

    /**
     *
     * @param ProviderUser $providerUser
     * @param string $provider
     * @return User
     * @throws NoEmailException
     * @throws EmailExistException
     * @throws Throwable
     */
    public function createUser(ProviderUser $providerUser, string $provider): User
    {
        // Sprawdzenie czy provider zwraca adres e-mail
        if (empty($providerUser->getEmail())) {
            throw new \N1ebieski\ICore\Exceptions\Socialite\NoEmailException();
        }

        // Sprawdzenie czy provider zwraca nazwę usera
        if (empty($providerUser->getName())) {
            throw new \N1ebieski\ICore\Exceptions\Socialite\NoNameException();
        }

        /** @var User */
        $user = $this->socialite->user()->make();

        // Sprawdzenie czy uzytkownik jest zarejestrowany pod tym adresem email
        /** @var User|null */
        $socialiteUser = $user->makeRepo()->firstByEmail($providerUser->getEmail());

        // Jesli tak, odrzucamy request i prosimy usera by powiazal konto z poziomu edycji profilu
        if ($socialiteUser) {
            throw new \N1ebieski\ICore\Exceptions\Socialite\EmailExistException();
        }

        // Jesli nie, tworzymy go
        return $this->db->transaction(function () use ($providerUser, $provider) {
            /** @var User */
            $socialiteUser = $this->socialite->user()->create([
                'name' => str_replace(' ', '_', $providerUser->getName() ?? ''),
                'email' => $providerUser->getEmail(),
            ]);

            $socialiteUser->assignRole('user');

            $socialiteUser->sendEmailVerificationNotification();

            // Tworzymy mu jeszcze powiazanie z Socialite
            $this->create([
                'provider_id'   => $providerUser->getId(),
                'provider_name' => $provider,
                'user' => $socialiteUser
            ]);

            return $socialiteUser;
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
