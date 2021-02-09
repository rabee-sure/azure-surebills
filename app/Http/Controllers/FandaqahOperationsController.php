<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Application;
use Illuminate\Http\Request;

class FandaqahOperationsController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function loginBySecret(Request $request, $secret, $secret2)
    {
        $app = Application::where('secret', $secret)->where('webhook_secret', $secret2)->first();

        if ($app) {
            \Auth::login($app->user);
        }

        return redirect('/');
    }
}
