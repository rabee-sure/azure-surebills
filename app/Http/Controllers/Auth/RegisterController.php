<?php

namespace App\Http\Controllers\Auth;

use App\Events\UserCreated;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Rules\PasswordRule;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Multicaret\Unifonic\UnifonicFacade;

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
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'business_name_en' => ['required', 'string', 'max:100'],
            'name' => ['required', 'string', 'max:50'],
            'email' => ['required', 'string', 'email', 'max:50', 'unique:users,email'],
            'mobile' => ['required', 'unique:users',
                // 'regex:/^((?:[+?0?0?966]+)(?:\s?\d{2})(?:\s?\d{7}))$/', //Saudi number with +966
                'regex:/(^[5]{1}[0-9]{8}$)/',
            ],
            'password' => [
                'required',
                'string',
                'min:8',
                new PasswordRule,
                'confirmed'
            ],
            'terms' => 'required',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        $source = app('router')->getRoutes()->match(app('request')->create(url()->previous()))->getName() == 'pos.register' ? 'pos' : 'sure bills';
        $user = User::create([
            'business_name_en' => $data['business_name_en'],
            'name'             => $data['name'],
            'email'            => $data['email'],
            'mobile'           => $data['mobile'],
            'source'           => $source,
            'password'         => Hash::make($data['password']),
            'able_refund_with_fees' => false,
        ]);
        event(new UserCreated($user));
        $user->sendMobileCode();

        if($user->source == 'pos')
        {
            $user->assignRole('pos super admin');
        }

        return $user;
    }
}
