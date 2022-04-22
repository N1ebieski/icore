<?php

namespace N1ebieski\ICore\Utils\Updater\Action\Types;

use Illuminate\Support\Str;
use N1ebieski\ICore\Utils\Updater\Action\Types\Interfaces\ActionInterface;

class RemoveFirst implements ActionInterface
{
    /**
     * Undocumented variable
     *
     * @var array
     */
    protected $action;

    /**
     * Undocumented variable
     *
     * @var Str
     */
    protected $str;

    /**
     * Undocumented function
     *
     * @param array $action
     * @param Str $str
     */
    public function __construct(array $action, Str $str)
    {
        $this->action = $action;

        $this->str = $str;
    }

    /**
     * Undocumented function
     *
     * @param string $contents
     * @param array $matches
     * @return string
     */
    public function __invoke(string $contents, array $matches): string
    {
        return $this->str->of($contents)->remove($matches[0]);
    }
}
