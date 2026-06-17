<?php

namespace App\Http\Controllers;

use App\Events\UserUpdateNotification;
use App\Http\Requests\AccountInformationRequest;
use App\Http\Requests\BankInformationRequest;
use App\Http\Requests\BusinessInformationRequest;
use App\Http\Requests\ChangePasswordRequest;
use App\Models\Media;
use App\Models\User;
use App\Rules\ValidateUploadFile;
use App\Support\Storage\ExportStoragePaths;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\Support\PathGenerator\PathGeneratorFactory;

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
        if (! $bankInfo->disable_bank_documents) {
            $uid = (int) $bankInfo->id;
            sync_merchant_disk_documents(
                $uid,
                'bank_documents',
                $request->input('document', []),
                function ($file) use ($uid) {
                    return merchant_bank_document_storage_candidates($file, $uid);
                }
            );
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

        return view('account.business_information', ['user' => $businessInfo]);
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
            delete_merchant_logo($businessInfo->logo);
            $businessInfo->update([
                'logo' => store_merchant_logo($request->file('logo'), (int) auth()->id()),
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
        if (! $businessInfo->disable_business_documents) {
            $uid = (int) $businessInfo->id;
            sync_merchant_disk_documents(
                $uid,
                'business_documents',
                $request->input('document', []),
                function ($file) use ($uid) {
                    return merchant_business_document_storage_candidates($file, $uid);
                }
            );
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
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function imagesUploadPost(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => ['required', new ValidateUploadFile(['pdf', 'png', 'jpeg', 'jpg', 'docx', 'doc', 'xlsx', 'csv'])],
            'upload_context' => ['nullable', 'string', 'in:business_documents,bank_documents'],
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $file = $request->file('file');
        $name = merchant_document_unique_filename($file);

        $disk = Storage::disk('public');
        $owner = auth()->user()->mainStoreUser ?? auth()->user();
        $uploadContext = $request->input('upload_context');

        if ($owner && in_array($uploadContext, ['business_documents', 'bank_documents'], true)) {
            $uid = (int) $owner->id;
            $directory = $uploadContext === 'business_documents'
                ? ExportStoragePaths::merchantBusinessDocumentUserPrefix($uid)
                : ExportStoragePaths::merchantBankDocumentUserPrefix($uid);
            if (! $disk->exists($directory)) {
                $disk->makeDirectory($directory);
            }
            $relativePath = $file->storeAs($directory, $name, 'public');
        } else {
            $dir = 'tmp/uploads';
            if (! $disk->exists($dir)) {
                $disk->makeDirectory($dir);
            }
            $relativePath = $disk->putFileAs($dir, $file, $name);
        }

        return response()->json([
            'name' => basename($relativePath),
            'path' => $relativePath,
            'original_name' => $file->getClientOriginalName(),
        ]);
    }

    public function downloadFile(Request $request, $id, $file_name)
    {
        $media = Media::query()
            ->where('id', $id)
            ->where('file_name', $file_name)
            ->firstOrFail();

        if ($media->model_type !== User::class || ! in_array($media->collection_name, ['business_documents', 'bank_documents'], true)) {
            abort(404);
        }

        $user = $request->user();
        $ownerId = (int) ($user->mainStoreUser ? $user->mainStoreUser->id : $user->id);
        if ((int) $media->model_id !== $ownerId) {
            abort(403);
        }

        $relativePath = PathGeneratorFactory::create($media)->getPath($media).$media->file_name;
        $relativePath = ltrim(str_replace('\\', '/', $relativePath), '/');

        abort_unless(Storage::disk('public')->exists($relativePath), 404);

        if (config('oci.enabled') && config('oci.visibility') === 'private') {
            try {
                $minutes = (int) config('oci.signed_url_expiration', 30);
                $url = Storage::disk('public')->temporaryUrl($relativePath, now()->addMinutes($minutes));

                return redirect()->away($url);
            } catch (\Throwable $e) {
                // Local disk or adapter without signed URLs: fall through to download response.
            }
        }

        return Storage::disk('public')->download($relativePath, $media->file_name);
    }

    /**
     * Download a merchant business/bank document stored on the public disk (admin-aligned paths).
     */
    public function downloadMerchantDocument(Request $request, string $collection, string $file)
    {
        if (! in_array($collection, ['business_documents', 'bank_documents'], true)) {
            abort(404);
        }

        $user = $request->user();
        $ownerId = (int) ($user->mainStoreUser ? $user->mainStoreUser->id : $user->id);

        $prefix = $collection === 'business_documents'
            ? ExportStoragePaths::merchantBusinessDocumentUserPrefix($ownerId)
            : ExportStoragePaths::merchantBankDocumentUserPrefix($ownerId);

        $prefixNorm = ltrim(str_replace('\\', '/', $prefix), '/');
        $fileName = basename(str_replace('\\', '/', rawurldecode($file)));
        if ($fileName === '' || strpos($fileName, '..') !== false) {
            abort(404);
        }

        $relativePath = $prefixNorm.'/'.$fileName;

        abort_unless(strpos($relativePath, $prefixNorm.'/') === 0, 404);
        abort_unless(Storage::disk('public')->exists($relativePath), 404);

        if (config('oci.enabled') && config('oci.visibility') === 'private') {
            try {
                $minutes = (int) config('oci.signed_url_expiration', 30);
                $url = Storage::disk('public')->temporaryUrl($relativePath, now()->addMinutes($minutes));

                return redirect()->away($url);
            } catch (\Throwable $e) {
                // fall through
            }
        }

        return Storage::disk('public')->download($relativePath, $fileName);
    }
}
