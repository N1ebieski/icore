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

namespace N1ebieski\ICore\Http\Requests\Admin\Role;

use Illuminate\Foundation\Http\FormRequest;
use N1ebieski\ICore\ValueObjects\Role\Name;

class UpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->role->name->isEditNotDefault();
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        if ($this->role->name->isUser()) {
            $this->merge([
                'name' => Name::USER
            ]);
        }

        if ($this->role->name->isApi()) {
            $this->merge([
                'name' => Name::API
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'bail|required|string|between:3,255|unique:roles,name,' . $this->role->id,
            'perm' => 'array|no_js_validation',
            'perm.*' => [
                'bail',
                'nullable',
                'string',
                'distinct',
                'exists:permissions,name',
                $this->role->name->isUser() ?
                    'regex:/^(web\.|api\.).+/'
                    : null,
                $this->role->name->isApi() ?
                    'regex:/^(api\.).+/'
                    : null,
                'no_js_validation'
            ]
        ];
    }
}
