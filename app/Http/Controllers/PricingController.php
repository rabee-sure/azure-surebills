<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Http\Resources\PricingResource;

class PricingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return view('pricing.index');
    }    

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function details(Request $request)
    {
        return new PricingResource(auth()->user());
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
    	$user = auth()->user();
    	$user->credit_cards_pay_fees = $request->credit_cards_pay_fees;
		$user->mada_pay_fees = $request->mada_pay_fees;
		$user->save();

        return new PricingResource($user);
    }
}
