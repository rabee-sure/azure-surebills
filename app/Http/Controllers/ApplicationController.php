<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Http\Requests\ApplicationRequest;
use App\Http\Resources\ApplicationResource;
use App\Models\MerchantSetting;
use Illuminate\Support\Str;

use function PHPUnit\Framework\isEmpty;

class ApplicationController extends Controller
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
        $applications = auth()->user()->mainStoreUser ? auth()->user()->mainStoreUser->applications()->with('channel')->get() : auth()->user()->applications()->with('channel')->get();
        return ApplicationResource::collection($applications);
    }

    /**
     * Store a newly created resource .
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ApplicationRequest $request)
    {
        $user_id = auth()->user()->store_main_user_id ?? auth()->user()->id;
        $merchantSettings = MerchantSetting::where('user_id', $user_id)->where('key', 'allow_create_integration_application')->first();
        if($merchantSettings->value == 0){
            return response()->json(['message' => 'Application Blocked! Please cantact adminstrator'], 423);
        }
        $application = new Application;
        $application->user_id = $user_id;
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
     * Update the specified resource .
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Application  $application
     * @return \Illuminate\Http\Response
     */
    public function update(ApplicationRequest $request, Application $application)
    {
        $this->authorize('updateMerchantApplication', $application);
        $application->name = $request->name;
        $application->redirect = $request->redirect;
        $application->fail_redirect_url = $request->fail_redirect_url;
        if($request->webhook_url && (!$application->webhook_secret || isEmpty($application->webhook_secret))){
            $application->webhook_url = $request->webhook_url;
            $application->webhook_secret = Str::random(20);
        }
        $application->save();

        return $application;
    }

    /**
     * Remove the specified resource.
     *
     * @param  \App\Models\Application  $application
     * @return \Illuminate\Http\Response
     */
    public function destroy(Application $application)
    {
        $this->authorize('deleteMerchantApplication', $application);
        return $application->delete();
    }
}
