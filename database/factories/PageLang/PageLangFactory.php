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

namespace N1ebieski\ICore\Database\Factories\PageLang;

use Illuminate\Database\Eloquent\Model;
use N1ebieski\ICore\Models\PageLang\PageLang;
use Illuminate\Database\Eloquent\Factories\Factory;

class PageLangFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<Model>
     */
    protected $model = PageLang::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        $content = $this->faker->text(2000);

        return [
            'title' => $this->faker->sentence(3),
            'content_html' => $content,
            'content' => $content,
            'seo_title' => $this->faker->randomElement([$this->faker->sentence(5), null]),
            'seo_desc' => $this->faker->text(),
        ];
    }
}
