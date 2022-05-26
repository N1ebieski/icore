<?php

namespace N1ebieski\ICore\Services\Rating;

use Illuminate\Database\Eloquent\Model;
use N1ebieski\ICore\Models\Rating\Rating;
use Illuminate\Contracts\Auth\Guard as Auth;
use Illuminate\Database\DatabaseManager as DB;
use N1ebieski\ICore\Services\Interfaces\CreateInterface;
use N1ebieski\ICore\Services\Interfaces\DeleteInterface;
use N1ebieski\ICore\Services\Interfaces\UpdateInterface;

class RatingService implements CreateInterface, UpdateInterface, DeleteInterface
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
        $this->rating = $rating;

        $this->auth = $auth;
        $this->db = $db;
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
            if ($this->rating->exists) {
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
}
