<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transfer;
use App\Rules\ValidateUploadFile;
use Illuminate\Http\Request;
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

            if ($validator->fails()){
                return response()->json(['error' =>$validator->errors()]);
            }


	        $file = $request->file('file');
	        $name = time().'-'.$file->getClientOriginalName();
	        $destinationPath = ($request->folder)? storage_path('/app/public/').$request->folder : storage_path('/app/public');
	        $file->move($destinationPath, $name);
	        return response()->json(['data' => ($request->folder)? $request->folder.'/'.$name : $name]);
	    }
    }

    /**
     * change Status.
     *
     * @param  \App\Models\Transfer  $Transfer
     * @return \Illuminate\Http\Response
     */
    public function uploadAttachment(Request $request, Transfer $transfer)
    {
        if ($request->hasFile('file')) {

            $validator = Validator::make($request->all(), [
                'file' => ['required', new ValidateUploadFile(['pdf', 'png', 'jpeg', 'jpg', 'docx', 'doc', 'xlsx', 'csv'])],
            ]);

            if ($validator->fails()){
                return response()->json(['error' =>$validator->errors()]);
            }

            $image = $request->file('file');
            $name = time().'.'.$image->getClientOriginalExtension();
            $destinationPath = storage_path('/app/public');
            $image->move($destinationPath, $name);
            $transfer->attachment = $name;
            $transfer->save();
            return response()->json(['data' => $name]);
        }
    }
}
