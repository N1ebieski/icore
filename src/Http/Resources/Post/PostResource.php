<?php

namespace N1ebieski\ICore\Http\Resources\Post;

use N1ebieski\ICore\Models\Post;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Config;
use Illuminate\Http\Resources\Json\JsonResource;
use N1ebieski\ICore\Http\Resources\User\UserResource;

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
            'seo_nofollow' => $this->seo_nofolow->getValue(),
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
                    return [
                        'user' => App::make(UserResource::class, ['user' => $this->user->setAttribute('depth', 1)])
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
