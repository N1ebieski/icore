<?php

namespace N1ebieski\ICore\Utils\Updater\Action\Types\Interfaces;

interface ActionInterface
{
    /**
     * 
     * @param string $contents 
     * @param array $matches 
     * @return string 
     */
    public function handle(string $contents, array $matches): string;
}
