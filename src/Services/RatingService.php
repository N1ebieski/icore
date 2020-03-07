<?php

namespace N1ebieski\ICore\Services;

use N1ebieski\ICore\Models\Rating\Rating;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Guard as Auth;
use N1ebieski\ICore\Services\Interfaces\Creatable;
use N1ebieski\ICore\Services\Interfaces\Updatable;
use N1ebieski\ICore\Services\Interfaces\Deletable;

/**
 * [RatingService description]
 */
class RatingService implements Creatable, Updatable, Deletable
{
    /**
     * [private description]
     * @var Auth
     */
    protected $auth;

    /**
     * [private description]
     * @var Rating
     */
    protected $rating;

    /**
     * [__construct description]
     * @param Rating $rating [description]
     * @param Auth   $auth   [description]
     */
    public function __construct(Rating $rating, Auth $auth)
    {
        $this->rating = $rating;

        $this->auth = $auth;
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
        if ($this->findByUser()) {
            if ($this->rating->rating == $attributes['rating']) {
                $rating = $this->delete();
            } else {
                $rating = $this->update($attributes);
            }
        } else {
            $rating = $this->create($attributes);
        }

        return $rating;
    }

    /**
     * Wyszukuje zapisaną wcześniej ocenę danego usera. W zależności od tego
     * wykonana zostanie akcja (dodanie nowej, edytowanie istniejącej, usunięcie)
     *
     * @return Rating|null
     */
    public function findByUser() : ?Rating
    {
        $rating = $this->rating->getMorph()->makeRepo()
            ->firstRatingByUser($this->auth->user()->id);

        return $rating instanceof Rating ? $this->rating = $rating : null;
    }

    /**
     * Tworzy nową ocenę przypisaną do modelu i usera
     *
     * @param  array $attributes
     * @return Model
     */
    public function create(array $attributes) : Model
    {
        $this->rating->user()->associate($this->auth->user());
        $this->rating->morph()->associate($this->rating->getMorph());
        $this->rating->rating = $attributes['rating'];

        $this->rating->save();

        return $this->rating;
    }

    /**
     * Usuwa ocenę
     *
     * @return bool
     */
    public function delete() : bool
    {
        return $this->rating->delete();
    }

    /**
     * Edytuje istniejącą ocenę
     *
     * @param array $attributes
     * @return bool
     */
    public function update(array $attributes) : bool
    {
        return $this->rating->update([
            'rating' => $attributes['rating']
        ]);
    }
}
