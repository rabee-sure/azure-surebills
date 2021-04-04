<?php

namespace App\Http\Resources;

use App\Models\Bill;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class UserInformationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'is_complete_profile' => $this->is_complete_profile,
            'balance' => round($this->balance, 2),
            'license_type' => $this->license_type,
            'business_name_en' => $this->business_name_en,
            'business_name_ar' => $this->business_name_ar,
            'sector' => $this->sector,
            'website' => $this->website,
            'description' => $this->description,
            'business_address' => $this->business_address,
            'business_mobile' => $this->business_mobile,
            'vat_registration_number' => $this->vat_registration_number,
            'commercial_registry_expiry_date' => $this->commercial_registry_expiry_date->format('Y-m-d'),
            'bank_id' => $this->bank_id,
            'iban_number' => $this->iban_number,
            'beneficiary_name' => $this->beneficiary_name,
            'logo' => $this->logo,
            'disable_business_documents' => $this->disable_business_documents,
            'disable_bank_documents' => $this->disable_bank_documents,
            'business_documents' => $this->getDocuments($this->business_documents),
            'bank_documents' => $this->getDocuments($this->bank_documents),
        ];
    }

    public function getDocuments($items)
    {
        $array = [];
        foreach ($items as $item) {
            $array[] = [
                'full_url' => $item->getFullUrl(),
                'id' => $item->id
            ];
        }
        return $array;
    }
}
