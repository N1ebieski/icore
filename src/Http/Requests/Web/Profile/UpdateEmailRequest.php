<?php

namespace N1ebieski\ICore\Http\Requests\Web\Profile;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Lang;
use Illuminate\Foundation\Http\FormRequest;

class UpdateEmailRequest extends FormRequest
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
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        // Dodaje globalna flage zeby bledy pojawialy sie wylacznie w gornym
        // alercie, zamiast pojedynczych bledow pod inputami
        $this->session()->flash('alertErrors', 1);

        return [
            'email' => 'required|email|string|max:255|unique:users,email',
            'password_confirmation' => [ function ($attribute, $value, $fail) {
                if (!Hash::check($value, Auth::user()->password)) {
                    return $fail(Lang::get('icore::passwords.confirmation'));
                }
            } , 'required', 'no_js_validation']
        ];
    }
}
