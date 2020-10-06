<?php

namespace App\Http\Controllers;

use App\Bill;
use App\Application;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function landing()
    {
        if (auth()->user()) {
            return redirect('/home');
        }

        return view('landing/home');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $bills = Bill::where('user_id', auth()->user()->id)->orderBy('created_at', 'desc')->get();
        $latest = $bills->take(3);

        // dd($bills->pluck('id'));

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
     * Show contact page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function contact()
    {
        return view('landing/contact');
    }

    /**
     * Show privacy page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function privacy()
    {
        return view('landing/privacy');
    }

    /**
     * Show terms page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function terms()
    {
        return view('landing/terms');
    }
}
