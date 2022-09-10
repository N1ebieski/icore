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

use N1ebieski\ICore\Models\User;
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

        /** @var User */
        $user = $this->user();

        return [
            'email' => 'required|email|string|max:255|unique:users,email',
            'password_confirmation' => [ function ($attribute, $value, $fail) use ($user) {
                if (is_null($user->password)) {
                    throw new \Exception('Password cannot be null');
                }

                if (!Hash::check($value, $user->password)) {
                    return $fail(Lang::get('icore::passwords.confirmation'));
                }
            } , 'required', 'no_js_validation']
        ];
    }
}
