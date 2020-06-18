<?php

namespace App\Http\Controllers;

use App\Http\Requests\AccountInformationRequest;
use App\Http\Requests\BankInformationRequest;
use App\Http\Requests\BusinessInformationRequest;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Resources\UserResource;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AccountController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function account_information()
    {
        return view('account.account_information', ['user' => auth()->user()]);
    }    
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function storeAccountInformation(AccountInformationRequest $request)
    {
        auth()->user()->update([
            'name'=> $request->name,
            'email'=> $request->email,
            'gender'=> $request->gender,
        ]);
   
        return redirect('home');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function bank_information()
    {
        return view('account.bank_information', ['user' => auth()->user()]);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function storeBankInformation(BankInformationRequest $request)
    {
        auth()->user()->update([]);
   
        return redirect('home');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function business_information()
    {
        return view('account.business_information', ['user' => auth()->user()]);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function storeBusinessInformation(BusinessInformationRequest $request)
    {
        auth()->user()->update([
        ]);
   
        return redirect('home');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function changePassword()
    {
        return view('account.change_password', ['user' => auth()->user()]);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function storeChangePassword(ChangePasswordRequest $request)
    {
        auth()->user()->update(['password'=> Hash::make($request->new_password)]);
   
        return redirect('home');
    }
}
