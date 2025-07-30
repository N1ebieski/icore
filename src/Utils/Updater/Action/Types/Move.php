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
use N1ebieski\ICore\Utils\Updater\Action\ActionFactory;
use N1ebieski\ICore\Utils\Updater\Action\Types\Interfaces\ActionInterface;

class Move implements ActionInterface
{
    public function __construct(
        protected array $action,
        protected Str $str,
        protected ActionFactory $actionFactory
    ) {
        //
    }

    public function handle(string $contents, array $matches): string
    {
        $contents = $this->actionFactory->makeAction([
            'type' => 'removeFirst',
            'search' => $matches[0],
        ])->handle($contents, [$matches[0]]);

        $contents = $this->actionFactory->makeAction([
            'type' => $this->action['type'][1],
            'search' => $this->action['to'],
            'to' => $matches[0]
        ])->handle($contents, [$this->action['to']]);

        return $contents;
    }
}
