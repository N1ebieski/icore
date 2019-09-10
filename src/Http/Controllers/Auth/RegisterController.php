<?php

namespace N1ebieski\ICore\Http\Controllers\Auth;

use N1ebieski\ICore\Models\User;
use N1ebieski\ICore\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use N1ebieski\ICore\Http\ViewComponents\CaptchaComponent as Captcha;
use N1ebieski\ICore\Http\Requests\Auth\Register\StoreRequest;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    protected $captcha;

    /**
     * Create a new controller instance.
     *
     * @param  Captcha $captcha
     * @return void
     */
    public function __construct(Captcha $captcha)
    {
        $this->middleware('icore.guest');
        $this->captcha = $captcha;
    }

    public function showRegistrationForm()
    {
        return view('icore::auth.register');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        //Antywzorzec, ale konieczny ze wzgledu na jsvalidation, ktory wymaga reguly w FormRequest
        $register = new StoreRequest;

        return Validator::make($data, array_merge($register->rules(),
            $this->captcha->toRules()), [], $this->captcha->toAttributes());
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \N1ebieski\ICore\Models\User
     */
    protected function create(array $data)
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'status' => 1
        ]);

        $user->assignRole('user');

        return $user;
    }
}
