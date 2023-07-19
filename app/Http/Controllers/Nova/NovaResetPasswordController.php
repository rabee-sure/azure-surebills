<?php

namespace App\Http\Controllers\Nova;

use App\Rules\CheckPasswordHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Laravel\Nova\Http\Controllers\ResetPasswordController;
use Illuminate\Validation\Rules;

class NovaResetPasswordController extends ResetPasswordController
{
    private $email;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('nova.guest:'.config('nova.guard'));
    }

    public function reset(Request $request)
    {
        $this->email = $request->email;
        $request->validate($this->rules(), $this->validationErrorMessages());

        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise we will parse the error and return the response.
        $response = $this->broker()->reset(
            $this->credentials($request), function ($user, $password) {
                $this->resetPassword($user, $password);
            }
        );

        // If the password was successfully reset, we will redirect the user back to
        // the application's home authenticated view. If there is an error we can
        // redirect them back to where they came from with their error message.
        return $response == Password::PASSWORD_RESET
                    ? $this->sendResetResponse($request, $response)
                    : $this->sendResetFailedResponse($request, $response);
    }

    /**
     * Get the password reset validation rules.
     *
     * @return array
     */
    protected function rules()
    {
        return [
            'token' => 'required',
            'email' => 'required|email',
            'password' => [
                'required', 
                'confirmed', 
                Rules\Password::min(15)
                ->letters()
                ->mixedCase()
                ->numbers()
                ->symbols(),
                new CheckPasswordHistory($this->email)
            ],
        ];
    }
}
