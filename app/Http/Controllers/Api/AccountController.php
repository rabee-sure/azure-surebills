<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AccountInformationRequest;
use App\Http\Requests\BankInformationRequest;
use App\Http\Requests\BusinessInformationRequest;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\UpdateInformationRequest;
use App\Http\Resources\UserInformationResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Rules\ValidateUploadFile;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AccountController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getInformation(Request $request)
    {
        return new UserInformationResource($request->user());
    }
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function updateInformation(UpdateInformationRequest $request)
    {        
        $user = $request->user();

        if($request->type == 'bank' || $request->type == "all"){
            $user->update([
                'bank_id' => $request->get('bank_id'),
                'iban_number' => $request->get('iban_number'),
                'beneficiary_name' => $request->get('beneficiary_name'),
            ]);
        }

        if($request->type == 'business' || $request->type == "all"){
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
                'logo' => $request->get('logo'),
            ]);
        }


        if (!$user->disable_bank_documents){
            $bank_documents = $request->input('bank_documents', []);
            //delete if Deleted
            if (count($user->bank_documents) > 0 && count($bank_documents) > 0) {
                foreach ($user->bank_documents as $media) {
                    if (!in_array($media->id, array_column($bank_documents, 'id'))) {
                        $media->delete();
                    }
                }
            }
            //create
            foreach ($bank_documents as $file) {
                if($file['id'] == null && isset($file['file'])){
                    $user->addMedia(storage_path('app/public/'.$file['file']))->toMediaCollection('bank_documents');        
                }
            }       
        }

        if (!$user->disable_business_documents){
            $business_documents = $request->input('business_documents', []);
            //delete if Deleted
            if (count($user->business_documents) > 0 && count($business_documents) > 0) {
                foreach ($user->business_documents as $media) {
                    if (!in_array($media->id, array_column($business_documents, 'id'))) {
                        $media->delete();
                    }
                }
            }
            //create
            foreach ($business_documents as $file) {
                if($file['id'] == null && isset($file['file'])){
                    $user->addMedia(storage_path('app/public/'.$file['file']))->toMediaCollection('business_documents');        
                }
            }       
        }

        return new UserInformationResource($user);
    }
}
