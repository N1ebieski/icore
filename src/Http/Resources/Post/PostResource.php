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

namespace N1ebieski\ICore\Http\Resources\Post;

use N1ebieski\ICore\Models\Post;
use N1ebieski\ICore\Models\User;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Config;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Post
 */
class PostResource extends JsonResource
{
    /**
     * Undocumented function
     *
     * @param Post $post
     */
    public function __construct(Post $post)
    {
        parent::__construct($post);
    }

    /**
     * Transform the resource into an array.
     *
     * @responseField id int
     * @responseField title string
     * @responseField slug string
     * @responseField short_content string A shortened version of the post without HTML formatting.
     * @responseField content string Post without HTML formatting.
     * @responseField content_html string Post with HTML formatting.
     * @responseField no_more_content_html string Post with HTML formatting without "show more" button.
     * @responseField less_content_html string Post with HTML formatting with "show more" button.
     * @responseField seo_title string Title for SEO.
     * @responseField meta_title string Title for META.
     * @responseField seo_desc string Description for SEO.
     * @responseField meta_desc string Description for META.
     * @responseField seo_noindex boolean Value for META.
     * @responseField seo_nofollow boolean Value for META.
     * @responseField status object Contains int value and string label.
     * @responseField comment boolean Determines whether comments are allowed.
     * @responseField first_image string Address of the first image in the post for META.
     * @responseField published_at string
     * @responseField created_at string
     * @responseField updated_at string
     * @responseField user object Contains relationship User author.
     * @responseField links object Contains links to resources on the website and in the administration panel.
     * @responseField meta object Paging, filtering and sorting information.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'title' => $this->title,
            'short_content' => $this->short_content,
            'content' => $this->content,
            'content_html' => $this->content_html,
            'no_more_content_html' => $this->no_more_content_html,
            'less_content_html' => $this->less_content_html,
            'seo_title' => $this->seo_title,
            'meta_title' => $this->meta_title,
            'seo_desc' => $this->seo_desc,
            'meta_desc' => $this->meta_desc,
            'seo_noindex' => $this->seo_noindex->getValue(),
            'seo_nofollow' => $this->seo_nofollow->getValue(),
            'status' => [
                'value' => $this->status->getValue(),
                'label' => Lang::get("icore::posts.status.{$this->status}")
            ],
            'comment' => $this->comment->getValue(),
            'first_image' => $this->first_image,
            'published_at' => $this->published_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            $this->mergeWhen(
                $this->relationLoaded('user'),
                function () {
                    /** @var User */
                    $user = $this->user->setAttribute('depth', 1);

                    return [
                        'user' => $user->makeResource()
                    ];
                }
            ),
            'links' => [
                $this->mergeWhen(
                    Config::get('icore.routes.web.enabled') === true && $this->status->isActive(),
                    function () {
                        return [
                            'web' => URL::route('web.post.show', [$this->slug])
                        ];
                    }
                ),
                $this->mergeWhen(
                    Config::get('icore.routes.admin.enabled') === true && optional($request->user())->can('admin.posts.view'),
                    function () {
                        return [
                            'admin' => URL::route('admin.post.index', ['filter[search]' => 'id:"' . $this->id . '"'])
                        ];
                    }
                )
            ]
        ];
    }
}
