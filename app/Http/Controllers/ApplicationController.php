<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Http\Requests\ApplicationRequest;
use App\Http\Resources\ApplicationResource;
use Illuminate\Support\Str;

class ApplicationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $applications = auth()->user()->applications;
        return ApplicationResource::collection($applications);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ApplicationRequest $request)
    {
        $application = new Application;
        $application->user_id = auth()->user()->id;
        $application->name = $request->name;
        $application->secret = Str::random(20);
        $application->redirect = $request->redirect;
        $application->fail_redirect_url = $request->fail_redirect_url;
        $application->webhook_secret = '';
        if($request->webhook_url){
            $application->webhook_url = $request->webhook_url;
            $application->webhook_secret = Str::random(20);
        }
        $application->save();
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Application  $application
     * @return \Illuminate\Http\Response
     */
    public function update(ApplicationRequest $request, Application $application)
    {
        $application->name = $request->name;
        $application->redirect = $request->redirect;
        $application->fail_redirect_url = $request->fail_redirect_url;
        $application->webhook_secret = '';
        if($request->webhook_url){
            $application->webhook_url = $request->webhook_url;
            $application->webhook_secret = Str::random(20);
        }
        $application->save();

        return $application;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Application  $application
     * @return \Illuminate\Http\Response
     */
    public function destroy(Application $application)
    {
        return $application->delete();
    }
}
