<?php

namespace N1ebieski\ICore\Http\Responses\Data\Post\Chart;

use Illuminate\Database\Eloquent\Collection;
use N1ebieski\ICore\Http\Responses\Data\DataInterface;
use Illuminate\Contracts\Translation\Translator as Lang;

class TimelineData implements DataInterface
{
    /**
     * Undocumented variable
     *
     * @var Collection
     */
    protected $collection;

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
     * @param Collection $collection
     * @param Lang $lang
     */
    public function __construct(Collection $collection, Lang $lang)
    {
        $this->collection = $collection;

        $this->lang = $lang;
    }

    /**
     * Undocumented function
     *
     * @return array
     */
    public function toArray(): array
    {
        $data = [];

        $this->collection->each(function ($item) use (&$data) {
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
