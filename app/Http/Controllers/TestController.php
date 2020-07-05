<?php

namespace App\Http\Controllers;

use App\Bill;
use App\Http\Requests\AccountInformationRequest;
use App\Http\Requests\BankInformationRequest;
use App\Http\Requests\BusinessInformationRequest;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Resources\UserResource;
use App\User;
use Carbon\Carbon;
use Hashids\Hashids;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Ramsey\Uuid\Uuid;

class TestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function test()
    {
        $bill = Bill::find('de24df7b-e771-4a29-abb1-6aaee1a854df');

        $uuid = Uuid::fromString($bill->id);
        $hex = $uuid->getHex();
        $hashids = new Hashids();
        $test = $hashids->encodeHex($hex);
        $test1 = $hashids->decodeHex($test);
        $test2 = $this->decode($test1);
        dd($test2);

        // $test = explode("-", $bill->id);
        // $test = implode(".", $test);
        // $test = strpos($bill->pay_id, '-');

        $test = explode("-", $bill->id);
        $test = implode("", $test);
        dd($test1);
    } 

    public function decode(string $hex): string
    {
        return array_reduce([20, 16, 12, 8], function ($uuid, $offset) {
            return substr_replace($uuid, '-', $offset, 0);
        }, str_pad($hex, 32, '0', STR_PAD_LEFT));
    }
 
}
