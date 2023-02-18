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

namespace N1ebieski\ICore\Http\Resources\Tag;

use N1ebieski\ICore\Models\Tag\Tag;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Tag
 */
class TagResource extends JsonResource
{
    /**
     * Undocumented function
     *
     * @param Tag $tag
     */
    public function __construct(Tag $tag)
    {
        parent::__construct($tag);
    }

    /**
     * Transform the resource into an array.
     *
     * @responseField id int
     * @responseField name string
     * @responseField slug string
     * @responseField lang string
     * @responseField created_at string
     * @responseField updated_at string
     * @responseField meta object Paging, filtering and sorting information.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->tag_id,
            'name' => $this->name,
            'slug' => $this->normalized,
            'lang' => $this->lang->getValue(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
