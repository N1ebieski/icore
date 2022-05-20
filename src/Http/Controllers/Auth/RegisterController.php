<?php

namespace N1ebieski\ICore\Http\Controllers\Auth;

use N1ebieski\ICore\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use N1ebieski\ICore\ValueObjects\Role\Name;
use Illuminate\Http\Response as HttpResponse;
use N1ebieski\ICore\ValueObjects\User\Status;
use Illuminate\Foundation\Auth\RegistersUsers;
use N1ebieski\ICore\ValueObjects\User\Marketing;
use N1ebieski\ICore\Http\Requests\Traits\HasCaptcha;
use N1ebieski\ICore\Http\Requests\Auth\Register\StoreRequest;

class RegisterController extends Controller
{
    use HasCaptcha;

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

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('icore.guest');
    }

    /**
     * Undocumented function
     *
     * @return HttpResponse
     */
    public function showRegistrationForm(): HttpResponse
    {
        return Response::view('icore::auth.register');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        $register = new StoreRequest();

        return Validator::make(
            $data,
            array_merge($register->rules(), $this->prepareCaptchaRules()),
        );
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
            'status' => Status::active(),
            'marketing' => $data['marketing_agreement'] ?? Marketing::inactive()
        ]);

        $user->assignRole(Name::USER);

        return $user;
    }
}
