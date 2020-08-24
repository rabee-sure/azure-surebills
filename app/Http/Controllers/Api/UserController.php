<?php

namespace App\Http\Controllers\Api;

use App\Application;
use App\Events\UserCreated;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UserController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'business_name' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'mobile' => ['unique:users'],
            'redirect_url' => ['required'],
            'webhook_url' => ['required'],
        ]);

        $user                  = new User;
        $user->business_name   = $request->business_name;
        $user->name            = $request->name;
        $user->email           = $request->email;
        $user->mobile          = $request->mobile;
        $user->fandaqah_user   = true;
        $user->password        = $request->email . $request->name;
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
}
