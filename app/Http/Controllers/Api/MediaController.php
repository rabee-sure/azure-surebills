<?php

namespace App\Http\Controllers\Api;

use App\Models\Transfer;
use Illuminate\Http\Request;
use App\Rules\ValidateUploadFile;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
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
        $validator = Validator::make($request->all(), [
            'file' => ['required', new ValidateUploadFile(['pdf', 'png', 'jpeg', 'jpg', 'docx', 'doc', 'xlsx', 'csv'])],
        ]);

        if ($validator->fails()){
            return response()->json(['error' =>$validator->errors()]);
        }
        
	    if ($request->hasFile('file')) {
	        $file = $request->file('file');
	        $name = time().'-'.$file->getClientOriginalName();
	        $destinationPath = ($request->folder)? storage_path('/app/public/').$request->folder : storage_path('/app/public');
	        $file->move($destinationPath, $name);
            $file_path = ($request->folder)? $request->folder.'/'.$name : $name;

	        return response()->json(['data' => Storage::disk('public')->exists($file_path) ? "storage/$file_path":$file_path]);
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

    public function getFile($guard ,$fileName)
    {
        if(auth()->guard($guard)->check())
        {
            if (Storage::disk('local')->exists($fileName)){
                return Storage::disk('local')->download($fileName);
            }
            abort(404);
        }
        return abort(401);
    }
}
