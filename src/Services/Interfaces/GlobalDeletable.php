<?php

namespace N1ebieski\ICore\Services\Interfaces;

interface GlobalDeletable
{
    /**
     * [deleteGlobal description]
     * @param  array $ids [description]
     * @return int        [description]
     */
    public function deleteGlobal(array $ids): int;
}
