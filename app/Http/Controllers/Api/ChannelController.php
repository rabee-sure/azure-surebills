<?php

namespace App\Http\Controllers\Api;

use App\Events\UserCreated;
use App\Exceptions\ValidationException;
use App\Http\Controllers\Controller;
use App\Http\Requests\ChannelApplicationAPiRequest;
use App\Http\Requests\ChannelUpdateApplicationPaymentFeesApiRequest;
use App\Http\Resources\ChannelApplicationResource;
use App\Http\Resources\ChannelResource;
use App\Http\Resources\TransactionResource;
use App\Models\Application;
use App\Models\Channel;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ChannelController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function show(Channel $channel, Request $request)
    {
        $application = Application::whereId($request->application_id)->whereSecret($request->application_secret)->first();


        if(isset($application) && $application->id == $channel->application_id){
            return new ChannelResource($channel);
        }else{
            return response()->json(['success' => false]);
        }
    }    

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Channel  $channel
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function transactions(Channel $channel, Request $request)
    {
        $token_channel = Channel::where('secret_token', $request->channel_token)->first();

        if(!isset($token_channel) || $token_channel->id != $channel->id){
           return response()->json([
                "message" => "The given data was invalid.",
                'errors' => [
                    'credential' =>[__('token or channel id is not correct')] 
                ] 
           ], 422);
        }

        $transactions =  Transaction::when($channel, function($q) use($channel){
                $q->whereHas('bill.application', function ( $query) use($channel){
                    $query->where('channel_id', $channel->id);
                });
            })
            ->get();

        return TransactionResource::collection($transactions)->additional(['meta' => [
            'balance' => round($transactions->where('type', 'credit')->sum('amount')-$transactions->where('type', 'debit')->sum('amount'), 2),
            'total_credit' => round($transactions->where('type', 'credit')->sum('amount'), 2),
            'total_debit' => round($transactions->where('type', 'debit')->sum('amount'), 2),
        ]]);
    }
    
    /**
     * Store a new sub account.
     *
     * @param  \App\Channel  $channel
     * @param  \Illuminate\Http\ChannelApplicationAPiRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function subAccount(Channel $channel, ChannelApplicationAPiRequest $request)
    {
        $channel = Channel::where('secret_token', $request->channel_token)->first();

        if(!isset($channel) || $channel->id != $channel->id){
           return response()->json([
                "message" => "The given data was invalid.",
                'errors' => [
                    'credential' =>[__('token or channel id is not correct')] 
                ] 
           ], 422);
        }

        $user = User::whereEmail($request->email)->orWhere('mobile', $request->mobile)->first();
        if($user == null){
            $validatedData = $request->validate([
                'business_name' => ['required', 'string', 'max:100'],
                'name' => ['required', 'string', 'max:50'],
                'email' => ['required', 'string', 'email', 'max:50', 'unique:users,email'],
                'mobile' => ['unique:users'],
            ]);

            $user = new User;
            $user->name = $request->name;
            $user->email = $request->email;
            $user->mobile = $request->mobile;
            $user->business_name_en = $request->business_name;
            $user->password = $request->email . $request->name;
            $user->from_channel_id = $channel->id;
            $user->able_refund = true;
            $user->able_refund_with_fees = false;
            $user->save();
            event(new UserCreated($user));
        }

        $application = Application::firstOrNew([
            'user_id' => $user->id,
            'channel_id' => $channel->id,
            'name' => $channel->name,
        ]);
        
        if($application->secret == null){
            $application->secret = Str::random(20);
        }
        $application->redirect = $request->redirect;
        $application->fail_redirect_url = $request->fail_redirect_url;
        $application->webhook_secret = '';
        if($request->webhook_url){
            $application->webhook_url = $request->webhook_url;
            if($application->webhook_secret == null){
                $application->webhook_secret = Str::random(20);
            }
        }

        $application->mada_fixed = $request->mada_fixed;
        $application->mada_percentage = $request->mada_percentage;
        $application->credit_cards_fixed = $request->credit_cards_fixed;
        $application->credit_cards_percentage = $request->credit_cards_percentage;

        $application->save();

        return [
            'account_id'     => $user->id,
            'client_id'      => $application->id,
            'secret'         => $application->secret,
            'webhook_secret' => $application->webhook_secret
        ];
    }

    /**
     * Store a new sub account.
     *
     * @param  \App\Channel  $channel
     * @param  \Illuminate\Http\ChannelApplicationAPiRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function updateSubAccountPaymentFees(Channel $channel, ChannelUpdateApplicationPaymentFeesApiRequest $request)
    {
        $token_channel = Channel::where('secret_token', $request->channel_token)->first();

        if(!isset($token_channel) || $token_channel->id != $channel->id){
           return response()->json([
                "message" => "The given data was invalid.",
                'errors' => [
                    'credential' =>[__('token or channel id is not correct')] 
                ] 
           ], 422);
        }

        $application = Application::find($request->application_id);

        $application->mada_fixed = $request->mada_fixed;
        $application->mada_percentage = $request->mada_percentage;
        $application->credit_cards_fixed = $request->credit_cards_fixed;
        $application->credit_cards_percentage = $request->credit_cards_percentage;

        $application->save();

        return [
            'mada_fixed' => $application->mada_fixed,
            'mada_percentage' => $application->mada_percentage,
            'credit_cards_fixed' => $application->credit_cards_fixed,
            'credit_cards_percentage' => $application->credit_cards_percentage,
        ];
    }
}
