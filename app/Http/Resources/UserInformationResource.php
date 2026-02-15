<?php

namespace App\Http\Resources;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Resources\Json\JsonResource;

class UserInformationResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'is_complete_profile' => $this->is_complete_profile,
            'balance' => round($this->balance, 2),
            'pending_balance' => round($this->pending_balance, 2),
            'license_type' => $this->license_type,
            'business_name_en' => $this->business_name_en,
            'business_name_ar' => $this->business_name_ar,
            'sector' => $this->sector,
            'website' => $this->website,
            'description' => $this->description,
            'business_address' => $this->business_address,
            'business_mobile' => $this->business_mobile,
            'vat_registration_number' => $this->vat_registration_number,
            'commercial_registry_expiry_date' => $this->commercial_registry_expiry_date
                ? $this->commercial_registry_expiry_date->format('Y-m-d')
                : null,
            'verified' => $this->verified,
            'bank_id' => $this->bank_id,
            'iban_number' => $this->iban_number,
            'beneficiary_name' => $this->beneficiary_name,

            'logo' => $this->getLogoUrl(),

            'disable_business_documents' => $this->disable_business_documents,
            'disable_bank_documents' => $this->disable_bank_documents,

            'business_documents' => $this->getDocuments($this->business_documents),
            'bank_documents' => $this->getDocuments($this->bank_documents),
        ];
    }

    private function getLogoUrl()
    {
        if (!$this->logo) {
            return null;
        }

        if (!Storage::disk('oci')->exists($this->logo)) {
            return null;
        }

        return Storage::disk('oci')
            ->temporaryUrl($this->logo, now()->addMinutes(10));
    }

    private function getDocuments($items)
    {
        return collect($items)->map(function ($item) {

            $path = $item->getPath();

            return [
                'id' => $item->id,
                'file_name' => $item->file_name,
                'url' => Storage::disk('oci')
                        ->temporaryUrl($path, now()->addMinutes(10)),
            ];
        })->values();
    }
}
