<?php

namespace N1ebieski\ICore\Repositories;

use N1ebieski\ICore\Models\Stat\Stat;

class StatRepo
{
    /**
     * [private description]
     * @var Stat
     */
    protected $stat;

    /**
     * [__construct description]
     * @param Stat $stat [description]
     */
    public function __construct(Stat $stat)
    {
        $this->stat = $stat;
    }

    /**
     * [firstBySlug description]
     * @param  string $slug [description]
     * @return Stat|null       [description]
     */
    public function firstBySlug(string $slug)
    {
        return $this->stat->where('slug', $slug)->first();
    }
}
