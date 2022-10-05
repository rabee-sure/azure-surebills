<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class PosController extends Controller
{
    public function __construct()
    {
        // $this->middleware('permission:show pos');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function categories(Request $request)
    {
        return view('pos.categories');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function products(Request $request)
    {
        return view('pos.products');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function discount(Request $request)
    {
        return view('pos.discount');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function quantity(Request $request)
    {
        return view('pos.quantity');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function pay(Request $request)
    {
        return view('pos.pay');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function bill(Request $request)
    {
        return view('pos.bill');
    }

    public function client(Request $request)
    {
        return view('pos.client');
    }

    public function redirectToProductsViaPos($redirectUuid)
    {
        $user = User::where('redirect_uuid', $redirectUuid)->first();
        if($user)
        {
            auth()->logout();
            auth()->login($user);
            $user->redirect_uuid = null;
            $user->save();
            return redirect(route('products.all'));
        }

        abort(404, 'Unauthorized');
    }

}
