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

namespace N1ebieski\ICore\Http\Responses\Data\Chart\Post;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Translation\Translator as Lang;
use N1ebieski\ICore\Http\Responses\Data\Chart\DataInterface;

class TimelineData implements DataInterface
{
    /**
     * Undocumented function
     *
     * @var array
     */
    protected $colors = [
        'posts' => 'rgb(255, 193, 7)',
        'pages' => 'rgb(40, 167, 69)'
    ];

    /**
     * Undocumented function
     *
     * @param Lang $lang
     */
    public function __construct(protected Lang $lang)
    {
        //
    }

    /**
     * Undocumented function
     *
     * @return array
     */
    public function toArray(Collection $collection): array
    {
        $data = [];

        $collection->each(function ($item) use (&$data) {
            $data[] = [
                'year' => $item->year,
                'month' => $item->month,
                'type' => [
                    'value' => $item->type,
                    'label' => $this->lang->get("icore::{$item->type}.route.index")
                ],
                'count' => $item->count,
                'color' => $this->colors[$item->type]
            ];
        });

        return $data;
    }
}
