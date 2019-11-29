<?php

namespace N1ebieski\ICore\Services;

use Laravel\Socialite\Contracts\User as ProviderUser;
use N1ebieski\ICore\Models\User;
use N1ebieski\ICore\Models\Socialite as Social;

/**
 * [SocialiteService description]
 */
class SocialiteService
{
    /**
     * [private description]
     * @var User
     */
    protected $user;

    /**
     * [private description]
     * @var Social
     */
    protected $socialite;

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
     * @var User
     */
    protected $socialiteUser;

    /**
     * [__construct description]
     * @param User   $user      [description]
     * @param Social $socialite [description]
     */
    public function __construct(User $user, Social $socialite)
    {
        $this->user = $user;
        $this->socialite = $socialite;
    }

    /**
     * [setSocialiteUser description]
     * @param User $socialiteUser [description]
     */
    public function setSocialiteUser(User $socialiteUser)
    {
        $this->socialiteUser = $socialiteUser;

        return $this;
    }

    /**
     * @param  ProviderUser $providerUser
     * @param  string $provider
     * @return User
     */
    public function findOrCreateUser(ProviderUser $providerUser, string $provider) : User
    {
        if (is_null($this->socialiteUser = $this->findUser($providerUser, $provider)))
        {
            $this->socialiteUser = $this->createUser();
        }

        return $this->socialiteUser;
    }

    /**
     * @param  ProviderUser $providerUser
     * @param  string $provider
     * @return User|null
     */
    public function findUser(ProviderUser $providerUser, string $provider) : ?User
    {
        $this->providerUser = $providerUser;
        $this->provider = $provider;

        // Sprawdzenie czy uzytkownik jest zarejestrowany jako Socialite
        $socialite = $this->socialite->makeRepo()->firstByProvider(
            $this->provider,
            $this->providerUser->getId()
        );

        if ($socialite) return $this->socialiteUser = $socialite->user;

        return null;
    }

    /**
     * @return User
     */
    public function createUser() : User
    {
        // Sprawdzenie czy provider zwraca adres e-mail
        if (empty($this->providerUser->getEmail())) {
            throw new \N1ebieski\ICore\Exceptions\Socialite\NoEmailException(
                'Provider has not provided the user\'s email address.'
            );
        }

        // Sprawdzenie czy uzytkownik jest zarejestrowany jako User
        $this->socialiteUser = $this->user->makeRepo()->firstByEmail($this->providerUser->getEmail());

        // Jesli tak, odrzucamy request i prosimy usera by powiazal konto z poziomu edycji profilu
        if ($this->socialiteUser) {
            throw new \N1ebieski\ICore\Exceptions\Socialite\EmailExistException(
                'There is a registered account for the email address provided.'
            );
        }

        // Jesli nie, tworzymy go
        $this->socialiteUser = $this->user->create([
            'name' => str_replace(' ', '_', $this->providerUser->getName()),
            'email' => $this->providerUser->getEmail(),
        ]);

        $this->socialiteUser->assignRole('user');
        $this->socialiteUser->sendEmailVerificationNotification();

        // Tworzymy mu jeszcze powiazanie z Socialite
        $this->create();

        return $this->socialiteUser;
    }

    /**
     * Tworzy powiazanie usera z socialite
     * @return Social
     */
    public function create() : Social
    {
        return $this->socialiteUser->socialites()->create([
            'provider_id'   => $this->providerUser->getId(),
            'provider_name' => $this->provider,
        ]);
    }
}
