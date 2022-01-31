<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Bill;
use App\Http\Requests\SettingsRequest;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
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
        return view('settings.index', ['user' => auth()->user()]);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function postSettings(SettingsRequest $request)
    {
        $settings = auth()->user()->settings;
        $settings->add_tax = $request->add_tax;
        $settings->tax_value = $request->tax_value;
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
        $settings->save();

        return redirect('/settings');
    }
}
