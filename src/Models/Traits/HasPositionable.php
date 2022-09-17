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

trait HasPositionable
{
    /**
     * [reorderSiblings description]
     * @return void [description]
     */
    public function reorderSiblings(): void
    {
        $originalPosition = $this->getOriginal('position');

        if (is_int($originalPosition)) {
            if ($this->position > $originalPosition) {
                $this->decrementSiblings($originalPosition, $this->position);
            } elseif ($this->position < $originalPosition) {
                $this->incrementSiblings($this->position, $originalPosition);
            }
        } else {
            $this->incrementSiblings($this->position, null);
        }
    }

    /**
     * [decrementSiblings description]
     * @param  int|null $from [description]
     * @param  int|null $to   [description]
     * @return int         [description]
     */
    public function decrementSiblings(int $from = null, int $to = null): int
    {
        return $this->siblings()
            ->when(!is_null($from), function ($query) use ($from) {
                $query->where('position', '>', $from);
            })
            ->when(!is_null($to), function ($query) use ($to) {
                $query->where('position', '<=', $to);
            })
            ->where('id', '<>', $this->id)
            ->decrement('position');
    }

    /**
     * [incrementSiblings description]
     * @param  int|null $from [description]
     * @param  int|null $to   [description]
     * @return int         [description]
     */
    public function incrementSiblings(int $from = null, int $to = null): int
    {
        return $this->siblings()
            ->when(!is_null($from), function ($query) use ($from) {
                $query->where('position', '>=', $from);
            })
            ->when(!is_null($to), function ($query) use ($to) {
                $query->where('position', '<', $to);
            })
            ->where('id', '<>', $this->id)
            ->increment('position');
    }

    /**
     * [countSiblings description]
     * @return int [description]
     */
    public function countSiblings(): int
    {
        return $this->siblings()->count();
    }

    /**
     * [getNextAfterLastPosition description]
     * @return int [description]
     */
    public function getNextAfterLastPosition(): int
    {
        $last = $this->siblings()
            ->orderBy('position', 'desc')
            ->first('position');

        return is_int($last?->position) ? $last->position + 1 : 0;
    }
}
