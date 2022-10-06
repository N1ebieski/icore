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

namespace N1ebieski\ICore\Models\Traits;

/**
 * Franzose/ClosureTable removes the real depth attribute feature since 6.0 version.
 * That trait restores that feature in combination with Observers.
 */
trait HasFixForRealDepthClosureTable
{
    /**
     * Undocumented function
     *
     * @return integer
     */
    public function getNextRealDepth(): int
    {
        // @phpstan-ignore-next-line
        $parent = $this->find($this->parent_id);

        // @phpstan-ignore-next-line
        return is_int($parent?->real_depth) ? $parent->real_depth + 1 : 0;
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function reorderRealDepths(): void
    {
        if ($this->parent_id !== $this->getOriginal('parent_id')) {
            $originalRealDepth = $this->getOriginal('real_depth');

            $amount = abs($this->real_depth - $originalRealDepth);

            if ($this->real_depth > $originalRealDepth) {
                $this->descendants()->where('id', '<>', $this->id)
                    ->increment('real_depth', $amount);
            } elseif ($this->real_depth < $originalRealDepth) {
                $this->descendants()->where('id', '<>', $this->id)
                    ->decrement('real_depth', $amount);
            }
        }
    }
}
