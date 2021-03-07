<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Rules\ValidateUploadFile;
use Illuminate\Support\Facades\Validator;

class MediaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function upload(Request $request)
    {
	    if ($request->hasFile('file')) {

            $validator = Validator::make($request->all(), [
                'file' => ['required', new ValidateUploadFile(['pdf', 'png', 'jpeg', 'jpg', 'docx', 'doc', 'xlsx', 'csv'])],
            ]);

            if ($validator->fails())
            {
                return response()->json(['error' =>$validator->errors()]);
            }

	        $image = $request->file('file');
	        $name = time().'.'.$image->getClientOriginalExtension();
	        $destinationPath = storage_path('/app/public');
	        $image->move($destinationPath, $name);
	        return response()->json(['data' => $name]);
	    }
    }

}
