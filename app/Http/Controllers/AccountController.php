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
    public function account(Request $request)
    {
        if($request->has('previous')){
            if($request->get('previous') == 2){
                session()->forget(auth()->user()->id.'_complete_profile_step_2');
            }elseif($request->get('previous') == 1){
                session()->forget(auth()->user()->id.'_complete_profile_step_1');
            }
        }
        if(auth()->user()->is_complete_profile){
            return view('account.account', ['user' => auth()->user()]);
        }else{
            if(session()->get(auth()->user()->id.'_complete_profile_step_2')){
                return view('account.steps.step3', ['user' => auth()->user()]);
            }elseif(session()->get(auth()->user()->id.'_complete_profile_step_1')){
                return view('account.steps.step2', ['user' => auth()->user()]);
            }else{
                return view('account.steps.step1', ['user' => auth()->user()]);
            }
        }
    }

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
        session()->put(auth()->user()->id.'_complete_profile_step_1', true);
        return redirect('/account');
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
            'bank_id' => $request->get('bank_id'),
            'iban_number' => $request->get('iban_number'),
            'beneficiary_name' => $request->get('beneficiary_name'),
        ]);

        if (count(auth()->user()->bank_documents) > 0) {
            foreach (auth()->user()->bank_documents as $media) {
                if (!in_array($media->file_name, $request->input('document', []))) {
                    $media->delete();
                }
            }
        }

        $media = auth()->user()->bank_documents->pluck('file_name')->toArray();

        foreach ($request->input('document', []) as $file) {
            if (count($media) === 0 || !in_array($file, $media)) {
                auth()->user()->addMedia(storage_path('tmp/uploads/' . $file))->toMediaCollection('bank_documents');
            }
        }

        if ($request->redirectHome) {
            return redirect('/');
        }

        return redirect('/account');
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
        if($request->hasFile('logo')) {
            $imageName = time().'_'.auth()->user()->id.'.'.$request->logo->extension();
            $image = $request->logo->move(public_path('uploads'), $imageName);
            auth()->user()->update([
                'logo' => 'uploads/'.$imageName,
            ]);
        }
        else
        {
            if($request->hidden_logo == null)
            {
                auth()->user()->update([
                    'logo' => null,
                ]);
            }
        }

        auth()->user()->update([
            'license_type' => $request->get('license_type'),
            'business_name_en' => $request->get('business_name_en'),
            'business_name_ar' => $request->get('business_name_ar'),
            'sector' => $request->get('sector'),
            'website' => $request->get('website'),
            'twitter' => $request->get('twitter'),
            'facebook' => $request->get('facebook'),
            'instagram' => $request->get('instagram'),
            'description' => $request->get('description'),
            'business_address' => $request->get('business_address'),
            'business_mobile' => $request->get('business_mobile'),
            'vat_registration_number' => $request->get('vat_registration_number'),
        ]);

        if (count(auth()->user()->business_documents) > 0) {
            foreach (auth()->user()->business_documents as $media) {
                if (!in_array($media->file_name, $request->input('document', []))) {
                    $media->delete();
                }
            }
        }

        $media = auth()->user()->business_documents->pluck('file_name')->toArray();

        foreach ($request->input('document', []) as $file) {
            if (count($media) === 0 || !in_array($file, $media)) {
                auth()->user()->addMedia(storage_path('tmp/uploads/' . $file))->toMediaCollection('business_documents');
            }
        }

        session()->put(auth()->user()->id.'_complete_profile_step_2', true);

        return redirect('/account');
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

        return redirect('/account');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function test_upload()
    {
        return view('account.test_upload', ['user' => auth()->user()]);
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function imagesUploadPost(Request $request)
    {
            $path = storage_path('tmp/uploads');

            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }

            $file = $request->file('file');

            $name = uniqid() . '_' . trim($file->getClientOriginalName());

            $file->move($path, $name);

            return response()->json([
                'name'          => $name,
                'original_name' => $file->getClientOriginalName(),
            ]);
    }
}
