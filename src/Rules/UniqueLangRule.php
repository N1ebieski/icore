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

namespace N1ebieski\ICore\Rules;

use Closure;
use Stringable;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Illuminate\Database\DatabaseManager as DB;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Contracts\Validation\InvokableRule;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\Translation\Translator as Lang;

class UniqueLangRule implements InvokableRule, Stringable
{
    /**
     *
     * @param Lang $lang
     * @param DB $db
     * @param Config $config
     * @param Str $str
     * @param string $table
     * @param string $column
     * @param null|int $ignore
     * @param null|Closure $query
     * @return void
     */
    public function __construct(
        protected Lang $lang,
        protected DB $db,
        protected Config $config,
        protected Str $str,
        protected string $table,
        protected string $column,
        protected ?int $ignore = null,
        protected ?Closure $query = null
    ) {
        //
    }

    /**
     * Run the validation rule.
    *
    * @param  string  $attribute
    * @param  mixed  $value
    * @param  \Closure  $fail
    * @return void
    */
    public function __invoke($attribute, $value, $fail)
    {
        if (!$this->passes($attribute, $value)) {
            $fail($this->lang->get('validation.unique', [$attribute]));
        }
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        $query = $this->db->table($this->table)
            ->join($this->getLangTable(), "{$this->getLangTable()}.{$this->getForeignKey()}", '=', "{$this->table}.id")
            ->where("{$this->getLangTable()}.{$this->column}", $value)
            ->where("{$this->getLangTable()}.lang", $this->config->get('app.locale'))
            ->when(!is_null($this->ignore), function (Builder $query) {
                return $query->where("{$this->table}.id", '<>', $this->ignore);
            });

        if (!is_null($this->query)) {
            $query = ($this->query)($query);
        }

        return $query->count() > 0 ? false : true;
    }

    /**
     *
     * @return string
     * @throws InvalidArgumentException
     */
    protected function getForeignKey(): string
    {
        return $this->str->singular($this->table) . '_id';
    }

    /**
     *
     * @return string
     */
    protected function getLangTable(): string
    {
        return $this->table . '_langs';
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    public function __toString(): string
    {
        return 'unique_lang';
    }
}
