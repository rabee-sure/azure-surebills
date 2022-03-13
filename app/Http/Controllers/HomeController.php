<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Application;
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
        $user = auth()->user();
        $bills = Bill::whereIn('user_id', $user->storeUsers(true))->orderBy('created_at', 'desc')->get();
        $latest = $bills->take(3);

        $balance = $user->balance;
        $total_paid = $bills->where('status', 'paid')->sum('total');
        $total_bills = $bills->count();
        $total_paid_bills = $bills->where('status', 'paid')->count();

        return view('home', [
            'user' =>  $user,
            'balance' =>  $balance ?? 0,
            'latest' =>  $latest,
            'bills' =>  $bills,
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
