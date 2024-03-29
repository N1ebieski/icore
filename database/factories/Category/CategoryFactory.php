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

namespace N1ebieski\ICore\Database\Factories\Category;

use Illuminate\Support\Facades\Config;
use N1ebieski\ICore\Models\Category\Category;
use N1ebieski\ICore\ValueObjects\AutoTranslate;
use N1ebieski\ICore\ValueObjects\Category\Status;
use Illuminate\Database\Eloquent\Factories\Factory;
use N1ebieski\ICore\Models\CategoryLang\CategoryLang;

class CategoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<Category>
     */
    protected $model = Category::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'status' => rand(Status::INACTIVE, Status::ACTIVE)
        ];
    }

    /**
     * Configure the model factory.
     *
     * @return static
     */
    public function configure()
    {
        return $this->afterCreating(function (Category $category) {
            foreach (Config::get('icore.multi_langs') as $lang) {
                CategoryLang::makeFactory()->for($category)->create([
                    'lang' => $lang
                ]);
            }
        });
    }

    /**
     * Undocumented function
     *
     * @return static
     */
    public function inactive()
    {
        return $this->state(function () {
            return [
                'status' => Status::INACTIVE
            ];
        });
    }

    /**
     * Undocumented function
     *
     * @return static
     */
    public function active()
    {
        return $this->state(function () {
            return [
                'status' => Status::ACTIVE
            ];
        });
    }

    /**
     * Undocumented function
     *
     * @return static
     */
    public function autoTrans()
    {
        return $this->state(function () {
            return [
                'auto_translate' => AutoTranslate::ACTIVE
            ];
        });
    }

    /**
     *
     * @return static
     */
    public function withoutLangs()
    {
        return $this->afterCreating(function (Category $category) {
            $category->langs()->delete();
        });
    }
}
