<?php

namespace N1ebieski\ICore\Http\Responses\Data\Chart\Post;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Translation\Translator as Lang;
use N1ebieski\ICore\Http\Responses\Data\Chart\DataInterface;

class TimelineData implements DataInterface
{
    /**
     * Undocumented variable
     *
     * @var Lang
     */
    protected $lang;

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
    public function __construct(Lang $lang)
    {
        $this->lang = $lang;
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
