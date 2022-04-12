<?php

namespace N1ebieski\ICore\Database\Factories\Post;

use Carbon\Carbon;
use N1ebieski\ICore\Models\Post;
use N1ebieski\ICore\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Post::class;

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
            'seo_desc' => $this->faker->text(),
            'seo_noindex' => rand(Post::INACTIVE, Post::ACTIVE),
            'seo_nofollow' => rand(Post::INACTIVE, Post::ACTIVE),
            'status' => rand(Post::INACTIVE, Post::ACTIVE),
            'comment' => rand(Post::INACTIVE, Post::ACTIVE),
            'published_at' => $this->faker->randomElement([
                $this->faker->dateTimeThisYear()->format('Y-m-d H:i:s'),
                null
            ]),
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
            $content = $this->faker->text(2000);

            $this->faker->addProvider(new \Mmo\Faker\PicsumProvider($this->faker));

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

    /**
     * Undocumented function
     *
     * @return static
     */
    public function active()
    {
        return $this->state(function () {
            return [
                'status' => Post::ACTIVE
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
                'comment' => Post::WITH_COMMENT
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
                'comment' => Post::WITHOUT_COMMENT
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
                'status' => Post::SCHEDULED,
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
        return $this->for(User::factory());
    }
}
