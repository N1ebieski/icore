<?php

namespace N1ebieski\ICore\Utils;

use Illuminate\Support\Str;
use N1ebieski\ICore\Cache\MigrationCache;

class MigrationUtil
{
    /**
     * Undocumented variable
     *
     * @var Str
     */
    protected $str;

    /**
     * Undocumented variable
     *
     * @var MigrationCache
     */
    protected $migrationCache;

    /**
     * Undocumented function
     *
     * @param MigrationCache $migrationCache
     * @param Str $str
     */
    public function __construct(MigrationCache $migrationCache, Str $str)
    {
        $this->migrationCache = $migrationCache;

        $this->str = $str;
    }

    /**
     * Undocumented function
     *
     * @param string $migration
     * @return boolean
     */
    public function contains(string $migration): bool
    {
        return $this->migrationCache->rememberAll()
            ->contains(function ($item) use ($migration) {
                return $this->str->contains($item->migration, $migration);
            });
    }
}
