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

namespace N1ebieski\ICore\Database\Factories\Post;

use Carbon\Carbon;
use N1ebieski\ICore\Models\Post;
use N1ebieski\ICore\Models\User;
use N1ebieski\ICore\Models\PostLang\PostLang;
use N1ebieski\ICore\ValueObjects\Post\Status;
use N1ebieski\ICore\ValueObjects\Post\Comment;
use N1ebieski\ICore\ValueObjects\Post\SeoNoindex;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @property \Faker\Generator&\Mmo\Faker\PicsumProvider $faker
 *
 */
class PostFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<Post>
     */
    protected $model = Post::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'seo_noindex' => rand(SeoNoindex::INACTIVE, SeoNoindex::ACTIVE),
            'seo_nofollow' => rand(SeoNoindex::INACTIVE, SeoNoindex::ACTIVE),
            'status' => rand(Status::INACTIVE, Status::ACTIVE),
            'comment' => rand(Comment::INACTIVE, Comment::ACTIVE),
            'published_at' => $this->faker->randomElement([
                $this->faker->dateTimeThisYear()->format('Y-m-d H:i:s'),
                null
            ]),
        ];
    }

    /**
     * Configure the model factory.
     *
     * @return $this
     */
    public function configure()
    {
        return $this->afterCreating(function (Post $post) {
            return PostLang::makeFactory()->for($post)->create();
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
    public function commentable()
    {
        return $this->state(function () {
            return [
                'comment' => Comment::ACTIVE
            ];
        });
    }

    /**
     * Undocumented function
     *
     * @return static
     */
    public function notCommentable()
    {
        return $this->state(function () {
            return [
                'comment' => Comment::INACTIVE
            ];
        });
    }

    /**
     * Undocumented function
     *
     * @return static
     */
    public function publish()
    {
        return $this->state(function () {
            return [
                'published_at' => $this->faker->dateTimeThisYear()->format('Y-m-d H:i:s')
            ];
        });
    }

    /**
     * Undocumented function
     *
     * @return static
     */
    public function scheduled()
    {
        return $this->state(function () {
            return [
                'status' => Status::SCHEDULED,
                'published_at' => Carbon::now()->format('Y-m-d H:i:s')
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
