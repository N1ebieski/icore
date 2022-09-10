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

namespace N1ebieski\ICore\Http\Clients\Intelekt\Post\Responses;

use N1ebieski\ICore\Http\Clients\Response;
use Illuminate\Support\Collection as Collect;

/**
 * @property object<data> $parameters
 * @property array $data
 *
 */
class IndexResponse extends Response
{
    /**
     * Undocumented function
     *
     * @param object $parameters
     * @param Collect $collect
     */
    public function __construct(object $parameters, protected Collect $collect)
    {
        parent::__construct($parameters);
    }

    /**
     * Undocumented function
     *
     * @param array $data
     * @return self
     */
    protected function setData(array $data): self
    {
        $this->parameters->data = $this->collect->make($data);

        return $this;
    }
}
