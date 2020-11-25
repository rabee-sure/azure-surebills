<?php

namespace App\Http\Controllers\Api;

use App\Application;
use App\Bill;
use App\Events\BillPaid;
use App\Events\UserCreated;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserStatResource;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TestController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function test(Request $request)
    {
        $bill = Bill::find('2d61c940-9bad-457f-9580-704e27075d94');
        event(new BillPaid($bill));
    }
}
