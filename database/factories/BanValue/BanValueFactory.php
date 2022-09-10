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

namespace N1ebieski\ICore\Database\Factories\BanValue;

use N1ebieski\ICore\Models\BanValue;
use N1ebieski\ICore\ValueObjects\BanValue\Type;
use Illuminate\Database\Eloquent\Factories\Factory;

class BanValueFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = BanValue::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            //
        ];
    }

    /**
     * Undocumented function
     *
     * @return static
     */
    public function ip()
    {
        return $this->state(function () {
            return [
                'type' => Type::IP,
                'value' => $this->faker->ipv4
            ];
        });
    }
}
