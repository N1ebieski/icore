<?php

namespace N1ebieski\ICore\Services;

use N1ebieski\ICore\Models\Stat\Stat;
use Illuminate\Database\DatabaseManager as DB;

class StatService
{
    /**
     * Model
     * @var Stat
     */
    protected $stat;

    /**
     * Undocumented variable
     *
     * @var DB
     */
    protected $db;

    /**
     * Undocumented function
     *
     * @param Stat $stat
     * @param DB $db
     */
    public function __construct(Stat $stat, DB $db)
    {
        $this->stat = $stat;

        $this->db = $db;
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    public function increment() : bool
    {
        $this->stat->getMorph()
            ->stats()
            ->syncWithoutDetaching([
                $this->stat->id => ['value' => $this->db->raw("`value` + 1")]
            ]);

        return true;
    }
}
