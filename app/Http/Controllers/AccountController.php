<?php

namespace App\Http\Controllers;

use App\Events\UserUpdateNotification;
use App\Http\Requests\AccountInformationRequest;
use App\Http\Requests\BankInformationRequest;
use App\Http\Requests\BusinessInformationRequest;
use App\Http\Requests\ChangePasswordRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Rules\ValidateUploadFile;
use Illuminate\Support\Str;

class AccountController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:update bank info', ['only' => ['bank_information', 'storeBankInformation']]);
        $this->middleware('permission:update business commercial info', ['only' => ['business_information', 'storeBusinessInformation']]);
    }

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
        $fields = config('accountfields.account_information');
        $user = auth()->user();
        $oldData = [];
        foreach($fields as $field){
            $oldData[$field] = $user->$field;
        }

        auth()->user()->update([
            'name'=> $request->name,
            'email'=> $request->email,
            'bullding_no' => $request->bullding_no,
            'street_name' => $request->street_name,
            'district' => $request->district,
            'city' => $request->city,
            'postal_code' => $request->postal_code,
            'additional_no' => $request->additional_no,
            'other_buyer_id' => $request->other_buyer_id,
        ]);
        $updatedData = [];
        $user = auth()->user();
        $updatedData = [];
        foreach($fields as $field){
            $updatedData[$field] = $user->$field;
        }

        //fire event send notification email for updated user's data
        event(new UserUpdateNotification($oldData, $updatedData, $user->id, 'Account Information'));

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
        if(auth()->user()->mainStoreUser)
        {
            $bankInfo = auth()->user()->mainStoreUser;
        }
        else
        {
            $bankInfo = auth()->user();
        }
        return view('account.bank_information', ['user' => $bankInfo]);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function storeBankInformation(BankInformationRequest $request)
    {
        if(auth()->user()->mainStoreUser)
        {
            $bankInfo = auth()->user()->mainStoreUser;
        }
        else
        {
            $bankInfo = auth()->user();
        }

        $fields = config('accountfields.bank_information');
        $user = auth()->user();
        $oldData = [];
        foreach($fields as $field){
            $oldData[$field] = $user->$field;
        }
        $oldData['documents'] = $user->bank_documents->pluck('file_name')->toArray();

        $bankInfo->update([
            'bank_id' => $request->get('bank_id'),
            'iban_number' => $request->get('iban_number'),
            'beneficiary_name' => $request->get('beneficiary_name'),
        ]);
        if (!$bankInfo->disable_bank_documents){
             if (count($bankInfo->bank_documents) > 0) {
                foreach ($bankInfo->bank_documents as $media) {
                    if (!in_array($media->file_name, $request->input('document', []))) {
                        $media->delete();
                    }
                }
            }

            $media = $bankInfo->bank_documents->pluck('file_name')->toArray();

            foreach ($request->input('document', []) as $file) {
                if (count($media) === 0 || !in_array($file, $media)) {
                    $bankInfo->addMediaFromDisk('tmp/uploads/' . $file, 'local')->toMediaCollection('bank_documents');
                }
            }
        }

        $updatedData = [];
        $user = auth()->user();
        $updatedData = [];
        foreach($fields as $field){
            $updatedData[$field] = $user->$field;
        }
        $updatedData['documents'] = $request->input('document', []);

        //fire event send notification email for updated user's data
        event(new UserUpdateNotification($oldData, $updatedData, $user->id, 'Bank Information'));


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
        if(auth()->user()->mainStoreUser)
        {
            $businessInfo = auth()->user()->mainStoreUser;
        }
        else
        {
            $businessInfo = auth()->user();
        }

        $logoUrl = null;

        if ($businessInfo->logo) {

            $logoUrl = \Illuminate\Support\Facades\Storage::disk('oci')->temporaryUrl($businessInfo->logo, now()->addMinutes(10));
        }

        $documents = $businessInfo->business_documents->map(function ($media) {
            return [
                'id'   => $media->id,
                'name' => $media->file_name,
                'url'  => \Illuminate\Support\Facades\Storage::disk('oci')->temporaryUrl($media->getPath(), now()->addMinutes(10)),
            ];
        });

        return view('account.business_information', ['user' => $businessInfo, 'logoUrl' => $logoUrl, 'documents' => $documents]);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function storeBusinessInformation(BusinessInformationRequest $request)
    {
        if(auth()->user()->mainStoreUser)
        {
            $businessInfo = auth()->user()->mainStoreUser;
        }
        else
        {
            $businessInfo = auth()->user();
        }

        $fields = config('accountfields.business_information');
        $user = auth()->user();
        $oldData = [];
        foreach($fields as $field){
            $oldData[$field] = $user->$field;
        }
        $oldData['documents'] = $user->business_documents->pluck('file_name')->toArray();

        if ($request->hasFile('logo')) {

            $file = $request->file('logo');

            $imageName = time().'_'.auth()->user()->id.'.'.$file->extension();

            $path = $file->storeAs(
                'uploads',
                $imageName,
                'oci'
            );

            $businessInfo->update([
                'logo' => $path,
            ]);
        }
        else
        {
            if($request->hidden_logo == null)
            {
                $businessInfo->update([
                    'logo' => null,
                ]);
            }
        }

        $businessInfo->update([
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
            'business_address_details' => $request->get('business_address_details'),
            'business_mobile' => $request->get('business_mobile'),
            'vat_registration_number' => $request->get('vat_registration_number'),
            'commercial_registry_expiry_date' => Carbon::createFromFormat('d/m/Y', $request->commercial_registry_expiry_date)->format('d-m-Y'),
        ]);
        if (!$businessInfo->disable_business_documents){
            if (count($businessInfo->business_documents) > 0) {
                foreach ($businessInfo->business_documents as $media) {
                    if (!in_array($media->file_name, $request->input('document', []))) {
                        $media->delete();
                    }
                }
            }

            $media = $businessInfo->business_documents->pluck('file_name')->toArray();

            foreach ($request->input('document', []) as $file) {
                if (count($media) === 0 || !in_array($file, $media)) {
                    $businessInfo->addMediaFromDisk('tmp/uploads/' . $file, 'local')->toMediaCollection('business_documents');
                }
            }
        }

        $updatedData = [];
        $user = auth()->user();
        $updatedData = [];
        foreach($fields as $field){
            $updatedData[$field] = $user->$field;
        }
        $updatedData['documents'] = $request->input('document', []);

        //fire event send notification email for updated user's data
        event(new UserUpdateNotification($oldData, $updatedData, $user->id, 'Business Information'));

        session()->put($businessInfo->id.'_complete_profile_step_2', true);

        if(auth()->user()->source == 'sure bills')
        {
            return redirect('/account');
        }
        else
        {
            return redirect('/');
        }
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

    public function imagesUploadPost(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => ['required', new ValidateUploadFile(['pdf', 'png', 'jpeg', 'jpg', 'docx', 'doc', 'xlsx', 'csv'])]
        ]);

        if ($validator->fails())
        {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $file = $request->file('file');
        $originalName = trim($file->getClientOriginalName());
        $fileName = uniqid() . '_' . $originalName;
        $folder = 'tmp/uploads';
        $path = $file->storeAs($folder, $fileName, 'local');
        return response()->json([
            'name'          => $fileName,
            'original_name' => $originalName,
            'path'          => $path,
        ]);
    }

    public function downloadFile($id, $file_name)
    {
        $path = storage_path('app/public/' . $id . '/' . $file_name);
        if (!file_exists($path)) {
            abort(404);
        }
        return response()->download($path);
    }
}
