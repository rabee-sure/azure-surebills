<?php

namespace App\Http\Controllers\Api;

use App\Application;
use App\Channel;
use App\Exceptions\ValidationException;
use App\Http\Controllers\Controller;
use App\Http\Requests\ChannelApplicationAPiRequest;
use App\Http\Resources\ChannelApplicationResource;
use App\Http\Resources\ChannelResource;
use App\User;
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeApplication(Channel $channel, ChannelApplicationAPiRequest $request)
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

        $user = User::where('email', $request->email)->first();
        if(!isset($user)){
           return response()->json([
                "message" => "The given data was invalid.",
                'errors' => [
                    'email' =>[__("We can't find a user with that e-mail address")] 
                ] 
           ], 422);
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

        return new ChannelApplicationResource($application);
    }
}
