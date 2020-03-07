<?php

namespace N1ebieski\ICore\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Contracts\Translation\Translator as Lang;

/**
 * [AlphaNumSpaces description]
 */
class AlphaNumSpacesRule implements Rule
{
    /**
     * Undocumented variable
     *
     * @var Lang
     */
    protected $lang;

    /**
     * Undocumented function
     *
     * @param Lang $lang
     */
    public function __construct(Lang $lang)
    {
        $this->lang = $lang;
    }

    /**
     * [validate description]
     * @param  [type] $attribute  [description]
     * @param  [type] $value      [description]
     * @param  [type] $parameters [description]
     * @param  [type] $validator  [description]
     * @return [type]             [description]
     */
    public function validate($attribute, $value, $parameters, $validator)
    {
        return $this->passes($attribute, $value);
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return preg_match('/^[\pL0-9\s]+$/u', $value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->lang->get('icore::validation.alpha_num_spaces');
    }
}
