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

namespace N1ebieski\ICore\Http\Resources\PostLang;

use N1ebieski\ICore\Models\PostLang\PostLang;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin PostLang
 */
class PostLangResource extends JsonResource
{
    /**
     * Undocumented function
     *
     * @param PostLang $postLang
     */
    public function __construct(PostLang $postLang)
    {
        parent::__construct($postLang);
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
            'progress' => $this->progress->getValue(),
            'lang' => $this->lang->getValue(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
