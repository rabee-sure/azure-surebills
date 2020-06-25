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
        auth()->user()->update([              
            'license_type' => $request->get('license_type'),
            'bank' => $request->get('bank'),
            'iban_number' => $request->get('iban_number'),
            'organization_name' => $request->get('organization_name'),
            'beneficiary_name' => $request->get('beneficiary_name'),
        ]);
   
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
        $imageName = time().'_'.auth()->user()->id.'.'.$request->logo->extension();  
        $image = $request->logo->move(public_path('images'), $imageName);
        
        auth()->user()->update([
            'business_name' => $request->get('business_name'),
            'sector' => $request->get('sector'),
            'website' => $request->get('website'),
            'twitter' => $request->get('twitter'),
            'facebook' => $request->get('facebook'),
            'instagram' => $request->get('instagram'),
            'description' => $request->get('description'),
            'business_address' => $request->get('business_address'),
            'business_mobile' => $request->get('business_mobile'),
            'vat_registration_number' => $request->get('vat_registration_number'),
            'logo' => 'images/'.$imageName,
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
