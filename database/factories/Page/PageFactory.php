<?php

namespace N1ebieski\ICore\Database\Factories\Page;

use N1ebieski\ICore\Models\User;
use N1ebieski\ICore\Models\Page\Page;
use Illuminate\Database\Eloquent\Factories\Factory;

class PageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Page::class;

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
            'seo_noindex' => rand(Page::SEO_NOINDEX, Page::SEO_INDEX),
            'seo_nofollow' => rand(Page::SEO_NOFOLLOW, Page::SEO_FOLLOW),
            'status' => rand(Page::INACTIVE, Page::ACTIVE),
            'comment' => rand(Page::WITHOUT_COMMENT, Page::WITH_COMMENT)
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
                'status' => Page::ACTIVE
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
                'comment' => Page::WITH_COMMENT
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
                'status' => Page::WITHOUT_COMMENT
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
