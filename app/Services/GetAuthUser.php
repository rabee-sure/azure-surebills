<?php

namespace App\Services;

use App\Models\User;

class GetAuthUser 
{
    /**
     * get auth user as per as request source.
     *
     * @return \Illuminate\Http\Response
     */
    public static function authUser($request)
    {
        $authUser = null;
        if($request->hasHeader('X-application-id') && $request->hasHeader('X-application-secret')){
            $authUser = $request->user;
        }elseif($request->hasHeader('Authorization')){
            $authUser = User::getAuthUser(request()->bearerToken());
        }else{
            $authUser = User::getAuthUser();
        }
        return $authUser;
    }
}
