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

namespace N1ebieski\ICore\Http\Requests\Web\Profile;

use Illuminate\Validation\Rule;
use N1ebieski\ICore\Models\User;
use Illuminate\Support\Facades\Config;
use Illuminate\Foundation\Http\FormRequest;
use N1ebieski\ICore\ValueObjects\User\Marketing;

class UpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * [prepareForValidation description]
     */
    protected function prepareForValidation(): void
    {
        if ($this->missing('marketing_agreement')) {
            $this->merge([
                'marketing_agreement' => Marketing::INACTIVE
            ]);
        }

        parent::prepareForValidation();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        /** @var User */
        $user = $this->user();

        return array_merge(
            [
                'name' => [
                    'required',
                    'alpha_dash',
                    'max:255',
                    'unique:users,name, ' . $user->id
                ],
                'marketing_agreement' => 'bail|nullable|boolean'
            ],
            count(Config::get('icore.multi_langs')) > 1 ? [
                'pref_lang' => [
                    'bail',
                    'nullable',
                    'string',
                    'min:2',
                    'max:2',
                    Rule::in(Config::get('icore.multi_langs'))
                ]
            ] : []
        );
    }
}
