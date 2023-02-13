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

namespace N1ebieski\ICore\Services\Interfaces;

use Illuminate\Database\Eloquent\Model;

interface LangServiceInterface
{
    /**
     *
     * @param array $attributes
     * @return Model
     */
    public function createOrUpdate(array $attributes): Model;

    /**
     *
     * @param array $attributes
     * @return Model
     */
    public function create(array $attributes): Model;

    /**
     *
     * @param array $attributes
     * @return Model
     */
    public function update(array $attributes): Model;

    /**
     *
     * @return null|bool
     */
    public function delete(): ?bool;
}
