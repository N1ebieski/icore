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

namespace N1ebieski\ICore\Services\Rating;

use Throwable;
use N1ebieski\ICore\Models\Rating\Rating;
use Illuminate\Database\DatabaseManager as DB;

class RatingService
{
    /**
     * Undocumented function
     *
     * @param Rating $rating
     * @param DB $db
     */
    public function __construct(
        protected Rating $rating,
        protected DB $db
    ) {
        //
    }

    /**
     * Tworzy, edytuje lub usuwa pojedynczą, należącą do zautentykowanego usera
     * ocenę modelu. Inicjalizacja odbywa się przez 1 link, stąd nie rozbijamy
     * tego na poszczególne metody kontrolera
     *
     * @param  array $attributes
     * @return mixed
     */
    public function createOrUpdateOrDelete(array $attributes): mixed
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
     *
     * @param array $attributes
     * @return Rating
     * @throws Throwable
     */
    public function create(array $attributes): Rating
    {
        return $this->db->transaction(function () use ($attributes) {
            $this->rating->user()->associate($attributes['user']);
            $this->rating->morph()->associate($attributes['morph']);

            $this->rating->rating = $attributes['rating'];

            $this->rating->save();

            return $this->rating;
        });
    }

    /**
     *
     * @return null|bool
     * @throws Throwable
     */
    public function delete(): ?bool
    {
        return $this->db->transaction(function () {
            return $this->rating->delete();
        });
    }

    /**
     *
     * @param array $attributes
     * @return Rating
     * @throws Throwable
     */
    public function update(array $attributes): Rating
    {
        return $this->db->transaction(function () use ($attributes) {
            $this->rating->update([
                'rating' => $attributes['rating']
            ]);

            return $this->rating;
        });
    }
}
