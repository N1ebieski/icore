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

namespace N1ebieski\ICore\Database\Factories\Page;

use N1ebieski\ICore\Models\User;
use N1ebieski\ICore\Models\Page\Page;
use N1ebieski\ICore\Models\PageLang\PageLang;
use N1ebieski\ICore\ValueObjects\Page\Status;
use N1ebieski\ICore\ValueObjects\Page\Comment;
use N1ebieski\ICore\ValueObjects\Page\SeoNoindex;
use N1ebieski\ICore\ValueObjects\Page\SeoNofollow;
use Illuminate\Database\Eloquent\Factories\Factory;

class PageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<Page>
     */
    protected $model = Page::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'seo_noindex' => rand(SeoNoindex::INACTIVE, SeoNoindex::ACTIVE),
            'seo_nofollow' => rand(SeoNofollow::INACTIVE, SeoNofollow::ACTIVE),
            'status' => rand(Status::INACTIVE, Status::ACTIVE),
            'comment' => rand(Comment::INACTIVE, Comment::ACTIVE)
        ];
    }

    /**
     * Configure the model factory.
     *
     * @return $this
     */
    public function configure()
    {
        return $this->afterCreating(function (Page $page) {
            return PageLang::makeFactory()->for($page)->create();
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
                'status' => Comment::INACTIVE
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
