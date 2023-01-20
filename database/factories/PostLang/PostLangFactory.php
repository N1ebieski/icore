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

namespace N1ebieski\ICore\Database\Factories\PostLang;

use Illuminate\Database\Eloquent\Model;
use N1ebieski\ICore\Models\PostLang\PostLang;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostLangFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<Model>
     */
    protected $model = PostLang::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        $content = $this->faker->text(2000);

        return [
            'title' => $this->faker->sentence(5),
            'content_html' => $content,
            'content' => strip_tags($content),
            'seo_title' => $this->faker->randomElement([$this->faker->sentence(5), null]),
            'seo_desc' => $this->faker->text()
        ];
    }

    /**
     * Undocumented function
     *
     * @return static
     */
    public function image()
    {
        return $this->state(function () {
            $this->faker->addProvider(new \Mmo\Faker\PicsumProvider($this->faker));

            $content = $this->faker->text(2000);

            $split = explode('. ', $content);
            $rands = (array)array_rand(array_slice($split, 0, array_key_last($split) - 5), rand(1, 3));

            $split = array_map(function ($item) use ($split) {
                return $item . ($item !== end($split) ? '.' : null);
            }, $split);

            foreach ($rands as $rand) {
                $image = ['</p><p><img src="' . $this->faker->picsumUrl(1920, 1080) . '" alt=""></p><p>'];

                array_splice($split, $rand, 0, $image);
            }

            $content = '<p>' . implode(' ', $split) . '</p>';

            return [
                'content_html' => $content,
                'content' => strip_tags($content),
            ];
        });
    }
}
