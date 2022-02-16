<?php

namespace N1ebieski\ICore\Models\Token;

trait WildcardAbilities
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
