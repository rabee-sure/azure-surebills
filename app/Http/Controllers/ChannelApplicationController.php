<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Channel;
use App\Http\Requests\ChannelApplicationRequest;
use App\Http\Resources\ChannelApplicationResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ChannelApplicationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Channel $channel)
    {
        $applications = $channel->applications()->with('user', 'channel')->get();
        return ChannelApplicationResource::collection($applications);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Channel $channel, ChannelApplicationRequest $request)
    {
        $user = User::where('email', $request->email)->first();
        
        $application = Application::firstOrNew([
            'user_id' => $user->id,
            'channel_id' => $channel->id,
            'name' => $channel->name,
        ]);
        $application->secret = Str::random(20);
        $application->redirect = $request->redirect;
        $application->fail_redirect_url = $request->fail_redirect_url;
        $application->webhook_secret = '';
        if($request->webhook_url){
            $application->webhook_url = $request->webhook_url;
            $application->webhook_secret = Str::random(20);
        }

        $application->mada_fixed = $request->mada_fixed;
        $application->mada_percentage = $request->mada_percentage;
        $application->credit_cards_fixed = $request->credit_cards_fixed;
        $application->credit_cards_percentage = $request->credit_cards_percentage;

        $application->save();
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\ChannelApplicationRequest  $request
     * @param  \App\Models\Application  $application
     * @return \Illuminate\Http\Response
     */
    public function update(ChannelApplicationRequest $request, Channel $channel, Application $application)
    {
        $application->redirect = $request->redirect;
        $application->fail_redirect_url = $request->fail_redirect_url;
        $application->webhook_secret = '';
        if($request->webhook_url){
            $application->webhook_url = $request->webhook_url;
            $application->webhook_secret = Str::random(20);
        }

        $application->mada_fixed = $request->mada_fixed;
        $application->mada_percentage = $request->mada_percentage;
        $application->credit_cards_fixed = $request->credit_cards_fixed;
        $application->credit_cards_percentage = $request->credit_cards_percentage;
        $application->save();

        return $application;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Application  $application
     * @return \Illuminate\Http\Response
     */
    public function destroy(Channel $channel, Application $application)
    {
        return $application->delete();
    }
}
