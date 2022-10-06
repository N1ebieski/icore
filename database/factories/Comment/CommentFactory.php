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

namespace N1ebieski\ICore\Database\Factories\Comment;

use N1ebieski\ICore\Models\User;
use N1ebieski\ICore\Models\Comment\Comment;
use N1ebieski\ICore\ValueObjects\Comment\Status;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<Comment>
     */
    protected $model = Comment::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        $content = $this->faker->text(300);

        return [
            'content_html' => $content,
            'content' => $content,
            'status' => rand(Status::INACTIVE, Status::ACTIVE)
        ];
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
    public function withUser()
    {
        return $this->for(User::makeFactory());
    }
}
