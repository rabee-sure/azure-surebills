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
<<<<<<< HEAD
        
	    if ($request->hasFile('file')) {
	        $file = $request->file('file');
	        $name = time().'-'.$file->getClientOriginalName();
            $folder = $this->sanitizePublicUploadFolder($request->folder);
            $disk = Storage::disk('public');
            if ($folder !== '') {
                $file_path = $disk->putFileAs($folder, $file, $name);
            } else {
                $file_path = $disk->putFileAs('', $file, $name);
            }

	        return response()->json(['data' => $disk->exists($file_path) ? "storage/$file_path" : $file_path]);
=======

	    if ($request->hasFile('file')) {
            $file = $request->file('file');
            $name = time() . '-' . $file->getClientOriginalName();
            $folder = $request->folder ? trim($request->folder, '/') : '';
            $file_path = $folder ? "$folder/$name" : $name;
            Storage::putFileAs($folder, $file, $name);

	        return response()->json(['data' => getFile($file_path)]);
>>>>>>> 79152f3b8ca19cc1464254750d139cfac6ccb9f4
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

<<<<<<< HEAD
            $image = $request->file('file');
            $name = time().'.'.$image->getClientOriginalExtension();
            Storage::disk('public')->putFileAs('', $image, $name);
            $transfer->attachment = $name;
=======
            $file = $request->file('file');
            $fileName = time().'.'.$file->getClientOriginalExtension();
            $folder = 'attachments';
            $path = $file->storeAs($folder, $fileName, 'oci');
            $transfer->attachment = $path;
>>>>>>> 79152f3b8ca19cc1464254750d139cfac6ccb9f4
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

    /**
     * Allow only safe subfolders under the public disk (no traversal).
     */
    protected function sanitizePublicUploadFolder(?string $folder): string
    {
        if ($folder === null || $folder === '') {
            return '';
        }

        $folder = str_replace(['..', '\\'], '/', (string) $folder);
        $folder = trim($folder, '/');

        if ($folder === '' || strpos($folder, '..') !== false) {
            return '';
        }

        return preg_replace('/[^a-zA-Z0-9_\/\-]/', '', $folder) ?: '';
    }
}
