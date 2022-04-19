<?php

namespace N1ebieski\ICore\Http\Responses\Data\Chart;

use Illuminate\Database\Eloquent\Collection;

interface DataInterface
{
    /**
     * Undocumented function
     *
     * @param Collection $collection
     * @return array
     */
    public function toArray(Collection $collection): array;
}
