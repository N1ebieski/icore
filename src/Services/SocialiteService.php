<?php

namespace N1ebieski\ICore\Services;

use N1ebieski\ICore\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Database\DatabaseManager as DB;
use N1ebieski\ICore\Models\Socialite as Social;
use N1ebieski\ICore\Services\Interfaces\Creatable;
use Laravel\Socialite\Contracts\User as ProviderUser;

class SocialiteService implements Creatable
{
    /**
     * [private description]
     * @var Social
     */
    protected $socialite;

    /**
     * Undocumented variable
     *
     * @var DB
     */
    protected $db;

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
     * Undocumented function
     *
     * @param Social $socialite
     * @param DB $db
     */
    public function __construct(Social $socialite, DB $db)
    {
        $this->socialite = $socialite;

        $this->db = $db;
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
            $this->socialiteUser = $this->socialite->user()->create([
                'name' => str_replace(' ', '_', $this->providerUser->getName()),
                'email' => $this->providerUser->getEmail(),
            ]);

            $this->socialiteUser->assignRole('user');
            $this->socialiteUser->sendEmailVerificationNotification();

            // Tworzymy mu jeszcze powiazanie z Socialite
            $this->create([]);

            return $this->socialiteUser;
        });
    }

    /**
     * Tworzy powiazanie usera z socialite
     * @param  array  $attributes
     * @return Model
     */
    public function create(array $attributes): Model
    {
        return $this->db->transaction(function () use ($attributes) {
            return $this->socialiteUser->socialites()->create([
                'provider_id'   => $this->providerUser->getId(),
                'provider_name' => $this->provider,
            ]);
        });
    }
}
