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
        $this->setStat($stat);

        $this->db = $db;
    }

    /**
     * Undocumented function
     *
     * @param Stat $stat
     * @return static
     */
    public function setStat(Stat $stat)
    {
        $this->stat = $stat;

        return $this;
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    public function increment(): bool
    {
        return $this->db->transaction(function () {
            $this->stat->morph
                ->stats()
                ->syncWithoutDetaching([
                    $this->stat->id => ['value' => $this->db->raw("`value` + 1")]
                ]);

            return true;
        });
    }
}
