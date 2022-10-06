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

namespace N1ebieski\ICore\Http\Requests\Admin\User;

use Illuminate\Validation\Rule;
use N1ebieski\ICore\Models\Role;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    /**
     * [__construct description]
     * @param Role $role [description]
     */
    public function __construct(protected Role $role)
    {
        parent::__construct();
    }


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
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['required', 'alpha_dash', 'max:255'],
            'email' => 'required|email|string|max:255|unique:users,email,' . $this->user->id,
            'roles' => 'required|array|min:1',
            'roles.*' => Rule::in($this->role->makeRepo()->getAvailableNamesAsArray())
        ];
    }
}
