<?php

namespace App\Http\Controllers\Api;

use App\Application;
use App\Bill;
use App\Events\BillStatusUpdated;
use App\Events\UserCreated;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserStatResource;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TestController extends Controller
{

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function test(Request $request)
    {
        $bill = Bill::find('2165d8c9-ea10-4bda-ab36-4f7ae40c422d');
        event( new BillStatusUpdated($bill) );
    }
}
