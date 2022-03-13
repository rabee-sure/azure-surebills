<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class IntegrationController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:show applications');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('integration.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function documentation()
    {
        return view('integration.documentation');
    }
}
