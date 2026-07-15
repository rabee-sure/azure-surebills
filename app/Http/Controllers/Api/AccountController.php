<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateInformationRequest;
use App\Http\Resources\UserInformationResource;
use App\Support\Storage\ExportStoragePaths;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

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

        if ($request->type == 'bank' || $request->type == 'all') {
            $user->update([
                'bank_id' => $request->get('bank_id'),
                'iban_number' => $request->get('iban_number'),
                'beneficiary_name' => $request->get('beneficiary_name'),
            ]);

            if (! $user->disable_bank_documents) {
                $uid = (int) $user->id;
                $docInputs = $request->input('document', []);
                if (is_array($docInputs) && count($docInputs) > 0) {
                    sync_merchant_disk_documents(
                        $uid,
                        'bank_documents',
                        $docInputs,
                        function ($file) use ($uid) {
                            return merchant_bank_document_storage_candidates($file, $uid);
                        }
                    );
                } elseif ($this->requestHasUploadedFiles($request->bank_documents)) {
                    merchant_replace_merchant_disk_documents_from_uploads($uid, 'bank_documents', (array) $request->bank_documents);
                }
<<<<<<< HEAD
=======

                //create
                foreach ($bank_documents as $file) {
                    $file_name = time() . '-' . $file->getClientOriginalName();
                    $folder = 'bank_documents';
                    $file->storeAs($folder, $file_name, 'oci');
                    try {
                        $user->addMedia($file_name)->toMediaCollection('bank_documents');
                    } catch (FileDoesNotExist $e) {
                        return [
                            "message" => "File Does Not Exist.",
                            "errors" => [
                                "bank_documents" => [
                                    $file. ' File Does Not Exist'
                                ]
                            ]
                        ];
                    }

                }
>>>>>>> 79152f3b8ca19cc1464254750d139cfac6ccb9f4
            }
        }

        if ($request->type == 'business' || $request->type == 'all') {
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

<<<<<<< HEAD
            if (! $user->disable_business_documents) {
                $uid = (int) $user->id;
                $docInputs = $request->input('document', []);
                if (is_array($docInputs) && count($docInputs) > 0) {
                    sync_merchant_disk_documents(
                        $uid,
                        'business_documents',
                        $docInputs,
                        function ($file) use ($uid) {
                            return merchant_business_document_storage_candidates($file, $uid);
                        }
                    );
                } elseif ($this->requestHasUploadedFiles($request->business_documents)) {
                    merchant_replace_merchant_disk_documents_from_uploads($uid, 'business_documents', (array) $request->business_documents);
                } else {
                    $this->syncBusinessDocumentsFromStructuredPayload($user, $request->input('business_documents', []));
                }
=======
            if (!$user->disable_business_documents ){
                $business_documents = $request->get('business_documents') ?? [];
                //delete if Deleted
                foreach ($user->business_documents as $media) {
                    if (!in_array($media->id, array_column($business_documents, 'id'))) {
                        $media->delete();
                    }
                }

                //create
                foreach ($business_documents as $file) {
                    if($file['id'] == null && isset($file['file'])){
                        $file_name =  str_replace('storage/','', $file['file']);
                        try {
                            $user->addMedia($file_name)->toMediaCollection('business_documents');
                        } catch (FileDoesNotExist $e) {
                            return [
                                "message" => "File Does Not Exist.",
                                "errors" => [
                                    "business_documents" => [
                                        $file['file']. ' File Does Not Exist'
                                    ]
                                ]
                            ];
                        }
                    }
                }
            }

            if(!$user->disable_business_documents && $request->business_documents){
                $business_documents = $request->business_documents;

                //first delete business_documents
                foreach ($user->business_documents as $media) {
                    $media->delete();
                }

                //create
                foreach ($business_documents as $file) {
                    $file_name = time() . '-' . $file->getClientOriginalName();
                    $folder = 'business_documents';
                    $file->storeAs($folder, $file_name, 'oci');
                    try {
                        $user->addMedia($file_name)->toMediaCollection('business_documents');
                    } catch (FileDoesNotExist $e) {
                        return [
                            "message" => "File Does Not Exist.",
                            "errors" => [
                                "business_documents" => [
                                    $file. ' File Does Not Exist'
                                ]
                            ]
                        ];
                    }

                }
>>>>>>> 79152f3b8ca19cc1464254750d139cfac6ccb9f4
            }
        }

        $user->refresh();

        return new UserInformationResource($user);
    }

    /**
     * @param  mixed  $files
     */
    private function requestHasUploadedFiles($files): bool
    {
        if (! is_array($files)) {
            return false;
        }
        foreach ($files as $file) {
            if ($file instanceof UploadedFile) {
                return true;
            }
        }

        return false;
    }

    /**
     * Legacy API: [{ id, file? }, ...] using crc32 ids from UserInformationResource.
     *
     * @param  array<int, mixed>  $items
     */
    private function syncBusinessDocumentsFromStructuredPayload($user, array $items): void
    {
        if ($items === []) {
            return;
        }
        if (! isset($items[0]) || ! is_array($items[0]) || ! array_key_exists('id', $items[0])) {
            return;
        }

        $uid = (int) $user->id;
        $keptIds = collect($items)->pluck('id')->filter(function ($id) {
            return $id !== null && $id !== '';
        })->map(function ($id) {
            return (int) $id;
        })->all();

        foreach (merchant_disk_documents_collection($uid, 'business_documents') as $doc) {
            if (! in_array((int) $doc->id, $keptIds, true)) {
                Storage::disk('public')->delete($doc->disk_relative_path);
            }
        }

        $prefix = ExportStoragePaths::merchantBusinessDocumentUserPrefix($uid);
        $prefixNorm = ltrim(str_replace('\\', '/', $prefix), '/');
        Storage::disk('public')->makeDirectory($prefix);
        $disk = Storage::disk('public');

        foreach ($items as $row) {
            if (! is_array($row)) {
                continue;
            }
            if (! empty($row['id'])) {
                continue;
            }
            if (! isset($row['file'])) {
                continue;
            }
            $file = $row['file'];
            if ($file instanceof UploadedFile) {
                $file->storeAs($prefix, merchant_document_unique_filename($file), 'public');

                continue;
            }
            if (! is_string($file)) {
                continue;
            }
            $path = ltrim(str_replace('\\', '/', str_replace('storage/', '', $file)), '/');
            if ($path === '' || strpos($path, '..') !== false) {
                continue;
            }
            $resolved = null;
            foreach (merchant_business_document_storage_candidates($path, $uid) as $candidate) {
                $candidate = ltrim(str_replace('\\', '/', $candidate), '/');
                if ($candidate !== '' && $disk->exists($candidate) && merchant_merchant_document_sync_path_allowed($candidate, $uid, 'business_documents')) {
                    $resolved = $candidate;
                    break;
                }
            }
            if ($resolved === null) {
                continue;
            }
            if (strpos($resolved, $prefixNorm.'/') === 0) {
                continue;
            }
            $dest = $prefixNorm.'/'.basename($resolved);
            $n = 0;
            while ($disk->exists($dest)) {
                $n++;
                $pi = pathinfo(basename($resolved));
                $ext = isset($pi['extension']) && $pi['extension'] !== '' ? '.'.$pi['extension'] : '';
                $stem = isset($pi['extension']) && $pi['extension'] !== '' ? substr(basename($resolved), 0, -strlen($ext)) : basename($resolved);
                $dest = $prefixNorm.'/'.$stem.'-'.$n.$ext;
            }
            $disk->move($resolved, $dest);
        }
    }
}
