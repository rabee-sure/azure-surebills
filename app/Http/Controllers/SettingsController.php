<?php

namespace App\Http\Controllers;

use App\Application;
use App\Bill;
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
        auth()->user()->settings()->update([
            'add_tax' => $request->add_tax,
            'tax_value' => $request->tax_value,
            'default_lang' => $request->default_lang,
            'active_lang' => $request->active_lang,
            'create_send_sms' => $request->create_send_sms,
            'create_send_email' =>  $request->create_send_email,
            'paid_send_sms' => $request->paid_send_sms,
            'paid_send_email' => $request->paid_send_email,
        ]);
        return redirect('/settings');
    }
}
