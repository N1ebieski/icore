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

namespace N1ebieski\ICore\Http\Resources\CategoryLang;

use Illuminate\Http\Resources\Json\JsonResource;
use N1ebieski\ICore\Models\CategoryLang\CategoryLang;

/**
 * @mixin CategoryLang
 */
class CategoryLangResource extends JsonResource
{
    /**
     * Undocumented function
     *
     * @param CategoryLang $categoryLang
     */
    public function __construct(CategoryLang $categoryLang)
    {
        parent::__construct($categoryLang);
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
            'name' => $this->name,
            'slug' => $this->slug,
            'progress' => $this->progress->getValue(),
            'lang' => $this->lang->getValue(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
