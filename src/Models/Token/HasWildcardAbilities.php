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

namespace N1ebieski\ICore\Models\Token;

trait HasWildcardAbilities
{
    /**
     * Determine if the token has a given ability.
     *
     * @param  string  $ability
     * @return bool
     */
    public function can($ability)
    {
        return in_array('*', $this->abilities) ||
            !empty(array_intersect($this->wildcardAbilities($ability), $this->abilities));
    }

    /**
     * Undocumented function
     *
     * @param string $ability
     * @return array
     */
    private function wildcardAbilities(string $ability): array
    {
        $a = explode('.', $ability);
        $elem = count($a);
        $n = $elem + 1;
        $b = [];

        while (--$n !== 0) {
            $b[$n - 1] = implode('.', array_slice($a, 0, $n)) . ($n !== $elem ? '.*' : null);
        }

        return array_values($b);
    }
}
