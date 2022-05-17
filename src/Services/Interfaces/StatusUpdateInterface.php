<?php

namespace N1ebieski\ICore\Services\Interfaces;

interface StatusUpdateInterface
{
    /**
     *
     * @param int $status
     * @return bool
     */
    public function updateStatus(int $status): bool;
}
