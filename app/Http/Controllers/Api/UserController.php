<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Settings;
use App\Events\UserCreated;
use App\Models\Application;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\PosUserResource;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function registerFandaqah(Request $request)
    {
        $validatedData = $request->validate([
            'business_name' => ['required', 'string', 'max:100'],
            'name' => ['required', 'string', 'max:50'],
            'email' => ['required', 'string', 'email', 'max:50', 'unique:users,email'],
            'mobile' => ['unique:users'],
            'redirect_url' => ['required'],
            'webhook_url' => ['required'],
        ]);

        $user                  = new User;
        $user->business_name_en   = $request->business_name;
        $user->name            = $request->name;
        $user->email           = $request->email;
        $user->mobile          = $request->mobile;
        $user->fandaqah_user   = true;
        $user->password        = $request->email . $request->name;
        $user->able_refund_with_fees = false;
        $user->save();
        event(new UserCreated($user));

        $application = new Application;
        $application->user_id           = $user->id;
        $application->name              = 'FANDAQAH';
        $application->secret            = Str::random(20);
        $application->redirect          = $request->redirect_url;
        $application->webhook_url       = $request->webhook_url;
        $application->webhook_secret    = Str::random(20);
        $application->save();

        return [
            'client_id'      => $application->id,
            'secret'         => $application->secret,
            'webhook_secret' => $application->webhook_secret
        ];
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateRedirect(Request $request)
    {
        $validatedData = $request->validate([
            'client_id'      => ['required'],
            'secret'         => ['required'],
            'webhook_secret' => ['required'],
            'redirect_url'   => ['required'],
            'webhook_url'    => ['required'],
        ]);

        $application = Application::where('id', $request->client_id)->where('secret', $request->secret)->where('webhook_secret', $request->webhook_secret)->first();
        if (!$application) {
            return false;
        }

        $application->redirect          = $request->redirect_url;
        $application->webhook_url       = $request->webhook_url;
        $application->save();

        return true;
    }


    /**
     * Store a new User.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $user = User::whereEmail($request->email)->orWhere('mobile', $request->mobile)->first();
        if($user == null){
            $validatedData = $request->validate([
                'business_name' => ['required', 'string', 'max:100'],
                'name' => ['required', 'string', 'max:50'],
                'email' => ['required', 'string', 'email', 'max:50', 'unique:users,email'],
                'mobile' => ['unique:users'],
            ]);

            $user = new User;
            $user->name = $request->name;
            $user->email = $request->email;
            $user->mobile = $request->mobile;
            $user->business_name_en = $request->business_name;
            $user->password = $request->email . $request->name;
            $user->able_refund_with_fees = false;
            $user->save();
            event(new UserCreated($user));
        }

        // api_bill_style
        if ($request->api_bill_style) {
            $settings = Settings::updateOrCreate([
                'user_id' => $user->id,
            ],[
                'api_bill_style' => true,
            ]);
        }

        $validatedData = $request->validate([
            'redirect_url' => ['required'],
            'webhook_url' => ['required'],
            'application_name' => ['required'],
            'channel_id' => ['nullable'],
        ]);

        $application = new Application;
        $application->user_id = $user->id;
        $application->name = $request->application_name;
        $application->secret = Str::random(20);
        $application->redirect = $request->redirect_url;
        $application->webhook_url = $request->webhook_url;
        $application->webhook_secret = Str::random(20);
        $application->channel_id = $request->channel_id;
        $application->mada_percentage = 3;
        $application->credit_cards_percentage = 3;
        $application->mada_fixed = 0;
        $application->credit_cards_fixed = 0;
        $application->save();

        return [
            'client_id'      => $application->id,
            'secret'         => $application->secret,
            'webhook_secret' => $application->webhook_secret
        ];
    }

    public function posLogin(Request $request)
    {
        $loginData = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (!Auth::attempt($loginData) || !Auth::user()->can('show pos')) {
            return response()->json(['message' => 'Credentials not match'], 401);
        }

        return new PosUserResource(Auth::user());
    }

}
