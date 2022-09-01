<?php

namespace N1ebieski\ICore\Models\Traits;

use Franzose\ClosureTable\Models\Entity;

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
        /**
         * @phpstan-ignore-next-line
         */
        $parent = $this->find($this->parent_id);

        return is_int(optional($parent)->real_depth) ? $parent->real_depth + 1 : 0;
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
