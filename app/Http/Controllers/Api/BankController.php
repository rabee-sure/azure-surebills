<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateInformationRequest;
use App\Http\Resources\BankResource;
use App\Http\Resources\UserInformationResource;
use App\Models\Bank;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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



        if (! $user->disable_bank_documents) {
            $uid = (int) $user->id;
            sync_merchant_disk_documents(
                $uid,
                'bank_documents',
                $request->input('document', []),
                function ($file) use ($uid) {
                    return merchant_bank_document_storage_candidates($file, $uid);
                }
<<<<<<< HEAD
            );
=======
            }

            $media = $user->bank_documents->pluck('file_name')->toArray();

            foreach ($request->input('document', []) as $file) {
                if (count($media) === 0 || !in_array($file, $media)) {
                    $user->addMedia('tmp/uploads/' . $file)->toMediaCollection('bank_documents');
                }
            }
>>>>>>> 79152f3b8ca19cc1464254750d139cfac6ccb9f4
        }

        if ($request->hasFile('logo')) {
            delete_merchant_logo($user->logo);
            $user->update([
                'logo' => store_merchant_logo($request->file('logo'), (int) $user->id),
            ]);
        } else {
            if($request->hidden_logo == null){
                $user->update([
                    'logo' => null,
                ]);
            }
        }

        if (! $user->disable_business_documents) {
            $uid = (int) $user->id;
            sync_merchant_disk_documents(
                $uid,
                'business_documents',
                $request->input('document', []),
                function ($file) use ($uid) {
                    return merchant_business_document_storage_candidates($file, $uid);
                }
<<<<<<< HEAD
            );
=======
            }

            $media = $user->business_documents->pluck('file_name')->toArray();

            foreach ($request->input('document', []) as $file) {
                if (count($media) === 0 || !in_array($file, $media)) {
                    $user->addMedia('tmp/uploads/' . $file)->toMediaCollection('business_documents');
                }
            }
>>>>>>> 79152f3b8ca19cc1464254750d139cfac6ccb9f4
        }

        return new UserInformationResource($user);
    }
}
