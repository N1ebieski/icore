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

namespace N1ebieski\ICore\Utils\Traits;

trait HasDecorator
{
    /**
     * Undocumented variable
     *
     * @var object
     */
    protected $decorated;

    /**
     * Undocumented function
     *
     * @return object
     */
    public function getDecorated(): object
    {
        $decorated = $this->decorated;

        while (is_a($decorated, get_class())) {
            $decorated = $decorated->getDecorated();
        }

        return $decorated;
    }

    /**
     * Undocumented function
     *
     * @param string $method
     * @param array $args
     * @return mixed
     */
    public function __call(string $method, array $args)
    {
        return call_user_func_array([$this->decorated, $method], $args);
    }

    /**
     * Undocumented function
     *
     * @param string $property
     * @return mixed
     */
    public function __get(string $property)
    {
        $decorated = $this->getDecorated();

        return $decorated->$property;
    }

    /**
     * Undocumented function
     *
     * @param string $property
     * @param mixed $value
     */
    public function __set(string $property, $value)
    {
        $decorated = $this->getDecorated();

        if (property_exists($decorated, $property)) {
            $decorated->$property = $value;
        } else {
            $this->$property = $value;
        }

        return $this;
    }
}
