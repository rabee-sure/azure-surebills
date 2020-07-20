<?php

namespace App\Http\Controllers;

use App\Bill;
use App\Application;
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
}
