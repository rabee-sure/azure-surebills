<?php

namespace App\Http\Controllers;

use App\Events\UserUpdateNotification;
use App\Models\Application;
use App\Models\Bill;
use App\Http\Requests\SettingsRequest;
use App\Models\Settings;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:update settings', ['only' => ['settings', 'postSettings']]);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function changeLang($lang)
    {
        if ($lang && in_array($lang, ['en', 'ar'])) {
            \App::setLocale($lang);
            session()->put('user-lang', $lang);
            // dd(app()->getLocale());
        }

        return redirect()->back();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function settings(Request $request)
    {
        $user = User::find(auth()->user()->store_main_user_id ?? auth()->user()->id);
        $imageUrl = '';
        if (!empty($user->settings->background_image_file)){
            $imageUrl = Storage::disk('oci')->temporaryUrl(
              $user->settings->background_image_file,
              now()->addMinutes(10)
            );
        }


        return view('settings.index', ['user' => $user, 'imageUrl' => $imageUrl]);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function postSettings(SettingsRequest $request)
    {
        $settings = Settings::userId(auth()->user()->store_main_user_id ?? auth()->user()->id)->first();
        // auth()->user()->settings;
        $settings->add_tax = $request->add_tax;
        $settings->tax_value = $request->tax_value;
        // $settings->add_tax_invoice = $request->add_tax_invoice;
        $settings->add_tax_invoice = false;
        $settings->default_lang = $request->default_lang;
        $settings->active_lang = $request->active_lang;
        $settings->create_send_sms = $request->create_send_sms;
        $settings->create_send_email =  $request->create_send_email;
        $settings->paid_send_sms = $request->paid_send_sms;
        $settings->paid_send_email = $request->paid_send_email;
        $settings->api_bill_style = $request->api_bill_style == "on" ? true : false;
        $settings->setTranslation('header_bill', 'en', $request->header_bill_en);
        $settings->setTranslation('header_bill', 'ar', $request->header_bill_ar);
        $settings->setTranslation('footer_bill', 'en', $request->footer_bill_en);
        $settings->setTranslation('footer_bill', 'ar', $request->footer_bill_ar);
        $settings->display_customer_details = $request->display_customer_details;
        $settings->background_color_body = $request->background_color_body;
        $settings->text_color_body = $request->text_color_body;
        $settings->background_color_payment_button = $request->background_color_payment_button;
        $settings->text_color_payment_button = $request->text_color_payment_button;

        // If delete_background_image is set, delete the image
        if ($request->delete_background_image == '1') {

            if ($settings->background_image_file &&
                Storage::disk('oci')->exists($settings->background_image_file)) {

                Storage::disk('oci')->delete($settings->background_image_file);
            }

            $settings->background_image_file = null;
        }

        // If new file uploaded
        elseif ($request->hasFile('background_image_file')) {

            // delete old file if exists
            if ($settings->background_image_file &&
                Storage::disk('oci')->exists($settings->background_image_file)) {

                Storage::disk('oci')->delete($settings->background_image_file);
            }

            $imageName = time().'_'.auth()->user()->id.'.'.$request->background_image_file->extension();

            $path = $request->background_image_file
                ->storeAs('uploads/bills_backgrounds', $imageName, 'oci');

            $settings->background_image_file = $path;
        }
        $settings->save();


        $fields = config('accountfields.tax_invoice_information');
        $user = auth()->user();
        $oldData = [];
        foreach($fields as $field){
            $oldData[$field] = $user->$field;
        }

        if($request->add_tax_invoice){
            auth()->user()->update([
                'bullding_no' => $request->bullding_no,
                'street_name' => $request->street_name,
                'district' => $request->district,
                'city' => $request->city,
                'postal_code' => $request->postal_code,
                'additional_no' => $request->additional_no,
                'other_buyer_id' => $request->other_buyer_id,
                'vat_registration_number' => $request->get('vat_registration_number'),
            ]);
        }

        $updatedData = [];
        $user = auth()->user();
        $updatedData = [];
        foreach($fields as $field){
            $updatedData[$field] = $user->$field;
        }

        //fire event send notification email for updated user's data
        event(new UserUpdateNotification($oldData, $updatedData, $user->id, 'Tax Invoice Information'));

        return redirect('/settings');
    }
}
