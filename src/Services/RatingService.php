<?php

namespace N1ebieski\ICore\Services;

use Illuminate\Database\Eloquent\Model;
use N1ebieski\ICore\Models\Rating\Rating;
use Illuminate\Contracts\Auth\Guard as Auth;
use Illuminate\Database\DatabaseManager as DB;
use N1ebieski\ICore\Services\Interfaces\Creatable;
use N1ebieski\ICore\Services\Interfaces\Deletable;
use N1ebieski\ICore\Services\Interfaces\Updatable;

class RatingService implements Creatable, Updatable, Deletable
{
    /**
     * [private description]
     * @var Auth
     */
    protected $auth;

    /**
     * Undocumented variable
     *
     * @var DB
     */
    protected $db;

    /**
     * [private description]
     * @var Rating
     */
    protected $rating;

    /**
     * Undocumented function
     *
     * @param Rating $rating
     * @param Auth $auth
     * @param DB $db
     */
    public function __construct(Rating $rating, Auth $auth, DB $db)
    {
        $this->setRating($rating);

        $this->auth = $auth;
        $this->db = $db;
    }

    /**
     * Undocumented function
     *
     * @param Rating $rating
     * @return static
     */
    public function setRating(Rating $rating)
    {
        $this->rating = $rating;

        return $this;
    }

    /**
     * Tworzy, edytuje lub usuwa pojedynczą, należącą do zautentykowanego usera
     * ocenę modelu. Inicjalizacja odbywa się przez 1 link, stąd nie rozbijamy
     * tego na poszczególne metody kontrolera
     *
     * @param  array $attributes
     * @return mixed
     */
    public function createOrUpdateOrDelete(array $attributes)
    {
        return $this->db->transaction(function () use ($attributes) {
            if ($this->findByUser()) {
                if ($this->rating->rating === (int)$attributes['rating']) {
                    $rating = $this->delete();
                } else {
                    $rating = $this->update($attributes);
                }
            } else {
                $rating = $this->create($attributes);
            }

            return $rating;
        });
    }

    /**
     * Tworzy nową ocenę przypisaną do modelu i usera
     *
     * @param  array $attributes
     * @return Model
     */
    public function create(array $attributes): Model
    {
        return $this->db->transaction(function () use ($attributes) {
            $this->rating->user()->associate($this->auth->user());
            $this->rating->morph()->associate($this->rating->morph);

            $this->rating->rating = $attributes['rating'];

            $this->rating->save();

            return $this->rating;
        });
    }

    /**
     * Usuwa ocenę
     *
     * @return bool
     */
    public function delete(): bool
    {
        return $this->db->transaction(function () {
            return $this->rating->delete();
        });
    }

    /**
     * Edytuje istniejącą ocenę
     *
     * @param array $attributes
     * @return bool
     */
    public function update(array $attributes): bool
    {
        return $this->db->transaction(function () use ($attributes) {
            return $this->rating->update([
                'rating' => $attributes['rating']
            ]);
        });
    }

    /**
     * Wyszukuje zapisaną wcześniej ocenę danego usera. W zależności od tego
     * wykonana zostanie akcja (dodanie nowej, edytowanie istniejącej, usunięcie)
     *
     * @return Rating|null
     */
    protected function findByUser(): ?Rating
    {
        $rating = $this->rating->morph->makeRepo()
            ->firstRatingByUser($this->auth->user()->id);

        return $rating instanceof Rating ? $this->rating = $rating : null;
    }
}
