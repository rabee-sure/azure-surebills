<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AccountInformationRequest;
use App\Http\Requests\BankInformationRequest;
use App\Http\Requests\BusinessInformationRequest;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\UpdateInformationRequest;
use App\Http\Resources\BankResource;
use App\Http\Resources\UserInformationResource;
use App\Http\Resources\UserResource;
use App\Models\Bank;
use App\Models\User;
use App\Rules\ValidateUploadFile;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class BankController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $banks = Bank::active()->get();
        return BankResource::collection($banks);
    }
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function updateInformation(UpdateInformationRequest $request)
    {        
        $user = $request->user();

        $user->update([
            'license_type' => $request->get('license_type'),
            'business_name_en' => $request->get('business_name_en'),
            'business_name_ar' => $request->get('business_name_ar'),
            'sector' => $request->get('sector'),
            'website' => $request->get('website'),
            'description' => $request->get('description'),
            'business_address' => $request->get('business_address'),
            'business_mobile' => $request->get('business_mobile'),
            'vat_registration_number' => $request->get('vat_registration_number'),
            'commercial_registry_expiry_date' => Carbon::parse($request->commercial_registry_expiry_date),

            'bank_id' => $request->get('bank_id'),
            'iban_number' => $request->get('iban_number'),
            'beneficiary_name' => $request->get('beneficiary_name'),
        ]);



        if (!$user->disable_bank_documents){
             if (count($user->bank_documents) > 0) {
                foreach ($user->bank_documents as $media) {
                    if (!in_array($media->file_name, $request->input('document', []))) {
                        $media->delete();
                    }
                }
            }

            $media = $user->bank_documents->pluck('file_name')->toArray();

            foreach ($request->input('document', []) as $file) {
                if (count($media) === 0 || !in_array($file, $media)) {
                    $user->addMedia(storage_path('tmp/uploads/' . $file))->toMediaCollection('bank_documents');
                }
            }       
        }

        if($request->hasFile('logo')) {
            $imageName = time().'_'.$user->id.'.'.$request->logo->extension();
            $image = $request->logo->move(public_path('uploads'), $imageName);
            $user->update([
                'logo' => 'uploads/'.$imageName,
            ]);
        }else{
            if($request->hidden_logo == null){
                $user->update([
                    'logo' => null,
                ]);
            }
        }

        if (!$user->disable_business_documents){
            if (count($user->business_documents) > 0) {
                foreach ($user->business_documents as $media) {
                    if (!in_array($media->file_name, $request->input('document', []))) {
                        $media->delete();
                    }
                }
            }

            $media = $user->business_documents->pluck('file_name')->toArray();

            foreach ($request->input('document', []) as $file) {
                if (count($media) === 0 || !in_array($file, $media)) {
                    $user->addMedia(storage_path('tmp/uploads/' . $file))->toMediaCollection('business_documents');
                }
            }
        }

        return new UserInformationResource($user);
    }
}
