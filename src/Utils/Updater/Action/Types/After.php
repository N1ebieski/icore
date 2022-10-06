<?php

/**
 * NOTICE OF LICENSE
 *
 * This source file is licenced under the Software License Agreement
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://intelekt.net.pl/pages/regulamin
 *
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * @author    Mariusz Wysokiński <kontakt@intelekt.net.pl>
 * @copyright Since 2019 INTELEKT - Usługi Komputerowe Mariusz Wysokiński
 * @license   https://intelekt.net.pl/pages/regulamin
 */

namespace N1ebieski\ICore\Utils\Updater\Action\Types;

use Illuminate\Support\Str;
use N1ebieski\ICore\Utils\Updater\Action\Types\Interfaces\ActionInterface;

class After implements ActionInterface
{
    /**
     * Undocumented function
     *
     * @param array $action
     * @param Str $str
     */
    public function __construct(
        protected array $action,
        protected Str $str
    ) {
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
    public function handle(string $contents, array $matches): string
    {
        foreach ($matches as $match) {
            $contents = $this->str->of($contents)->replace($match, $match . "\n" . $this->action['to']);
        }

        return $contents;
    }
}
