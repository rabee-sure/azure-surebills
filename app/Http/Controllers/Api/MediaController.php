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
            $name = time() . '-' . $file->getClientOriginalName();
            $folder = $request->folder ? trim($request->folder, '/') : '';
            $file_path = $folder ? "$folder/$name" : $name;
            Storage::putFileAs($folder, $file, $name);

	        return response()->json(['data' => getFile($file_path)]);
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

            $file = $request->file('file');
            $fileName = time().'.'.$file->getClientOriginalExtension();
            $folder = 'attachments';
            $path = $file->storeAs($folder, $fileName, 'oci');
            $transfer->attachment = $path;
            $transfer->save();
            return response()->json(['data' => $fileName]);
        }
    }

    public function getFile($guard ,$fileName)
    {
        if(auth()->guard($guard)->check())
        {
            if (Storage::exists($fileName)) {
                $fileContent = Storage::get($fileName);
                $mimeType = Storage::mimeType($fileName) ?? 'application/octet-stream';
                $downloadName = basename($fileName);

                return response($fileContent, 200)
                    ->header('Content-Type', $mimeType)
                    ->header('Content-Disposition', "attachment; filename=\"$downloadName\"");
            }

            abort(404);
        }
        return abort(401);
    }
}
