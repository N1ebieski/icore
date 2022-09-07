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

namespace N1ebieski\ICore\Filters\Traits;

trait HasParent
{
    /**
     *
     * @param int|null $id
     * @return void
     */
    public function filterParent(int $id = null): void
    {
        $this->parameters['parent'] = null;

        if ($id === 0) {
            $this->parameters['parent'] = 0;
        }

        if ($id !== null) {
            if ($parent = $this->findParent($id)) {
                $this->setParent($parent);
            }
        }
    }
}
