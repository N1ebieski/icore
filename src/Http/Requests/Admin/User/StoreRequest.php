<?php

namespace N1ebieski\ICore\Http\Requests\Admin\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use N1ebieski\ICore\Models\Role;

class StoreRequest extends FormRequest
{
    /**
     * [private description]
     * @var Role
     */
    protected $role;

    /**
     * [__construct description]
     * @param Role $role [description]
     */
    public function __construct(Role $role)
    {
        parent::__construct();

        $this->role = $role;
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
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'roles' => 'required|array|min:1',
            'roles.*' => Rule::in($this->role->makeRepo()->getAvailableNamesAsArray())
        ];
    }
}
