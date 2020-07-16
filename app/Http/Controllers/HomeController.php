<?php

namespace App\Http\Controllers;

use App\Bill;
use App\Application;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $bills = Bill::where('user_id', auth()->user()->id)->orderBy('id', 'desc')->get();
        $latest = $bills->take(3);

        $user_bills = auth()->user()->bills();
        $balance = auth()->user()->bills()->paid()->notSettled()->sum('total') - auth()->user()->bills()->notSettled()->sum('payment_fees');
        $total_paid = auth()->user()->bills()->paid()->sum('total');
        $total_bills = auth()->user()->bills()->count();
        $total_paid_bills = auth()->user()->bills()->paid()->count();

        return view('home', [
            'latest' =>  $latest,
            'bills' =>  $bills,
            'balance' =>  $balance,
            'total_paid' =>  $total_paid,
            'total_bills' =>  $total_bills,
            'total_paid_bills' =>  $total_paid_bills,
        ]);
    }

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
