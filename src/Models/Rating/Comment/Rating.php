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

namespace N1ebieski\ICore\Models\Rating\Comment;

use Illuminate\Database\Eloquent\Casts\Attribute;
use N1ebieski\ICore\Models\Rating\Rating as BaseRating;

/**
 * N1ebieski\ICore\Models\Rating\Comment\Rating
 *
 * @property int $id
 * @property int|null $user_id
 * @property int $model_id
 * @property string $model_type
 * @property int $rating
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string $poli
 * @property-read \N1ebieski\ICore\Models\Comment\Comment $morph
 * @property-read \N1ebieski\ICore\Models\User|null $user
 * @method static \N1ebieski\ICore\Database\Factories\Rating\RatingFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Rating newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Rating newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Rating poli()
 * @method static \Illuminate\Database\Eloquent\Builder|Rating poliType()
 * @method static \Illuminate\Database\Eloquent\Builder|Rating query()
 * @method static \Illuminate\Database\Eloquent\Builder|Rating whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Rating whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Rating whereModelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Rating whereModelType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Rating whereRating($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Rating whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Rating whereUserId($value)
 * @mixin \Eloquent
 */
class Rating extends BaseRating
{
    // Attributes

    /**
     *
     * @return Attribute
     */
    public function poli(): Attribute
    {
        return new Attribute(fn (): string => 'comment');
    }
}
