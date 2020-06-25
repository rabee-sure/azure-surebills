<?php

namespace App\Http\Controllers;

use App\Bill;
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

        $balance = Bill::where('user_id', auth()->user()->id)->paid()->sum('total');

        return view('home', [
            'latest' =>  $latest,
            'bills' =>  $bills,
            'balance' =>  $balance,
        ]);
    }
}
