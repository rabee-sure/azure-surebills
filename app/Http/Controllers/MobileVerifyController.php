<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use Illuminate\Http\Request;

class MobileVerifyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('mobile_verify', ['user' => new UserResource(auth()->user())]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
    	$user = auth()->user();
    	if($user->mobile_verified || $user->mobile_active_code == $request->pin){
    		$user->mobile_sent_at = null;
    		$user->save();
    		return response()->json([ 'success' => true]);
    	}
    	return response()->json([ 'success' => false]);
    }
}
