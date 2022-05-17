<?php

namespace N1ebieski\ICore\Services\Interfaces;

interface PositionUpdateInterface
{
    /**
     * 
     * @param int $position 
     * @return bool 
     */
    public function updatePosition(int $position): bool;
}
