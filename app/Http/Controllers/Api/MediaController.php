<?php

namespace App\Http\Controllers\Api;

use App\Models\Transfer;
use App\Rules\ValidateUploadFile;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class MediaController extends Controller
{
    /**
     * Upload generic file
     */
    public function upload(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => ['required', new ValidateUploadFile(['pdf','png','jpeg','jpg','docx','doc','xlsx','csv'])],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()
            ], 422);
        }

        if (!$request->hasFile('file')) {
            return response()->json(['error' => 'No file uploaded'], 400);
        }

        $file = $request->file('file');

        $name = time() . '-' . $file->getClientOriginalName();

        $folder = $request->folder
            ? trim($request->folder, '/')
            : '';

        $filePath = $folder ? "$folder/$name" : $name;

        Storage::disk('oci')->putFileAs($folder, $file, $name);

        $url = Storage::disk('oci')
            ->temporaryUrl($filePath, now()->addMinutes(10));

        return response()->json([
            'path' => $filePath,
            'url'  => $url,
        ]);
    }

    /**
     * Upload attachment for transfer
     */
    public function uploadAttachment(Request $request, Transfer $transfer)
    {
        $validator = Validator::make($request->all(), [
            'file' => ['required', new ValidateUploadFile(['pdf','png','jpeg','jpg','docx','doc','xlsx','csv'])],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()
            ], 422);
        }

        if (!$request->hasFile('file')) {
            return response()->json(['error' => 'No file uploaded'], 400);
        }

        $file = $request->file('file');

        $fileName = time().'.'.$file->getClientOriginalExtension();

        $folder = 'attachments';

        $filePath = "$folder/$fileName";

        Storage::disk('oci')->putFileAs($folder, $file, $fileName);

        $transfer->attachment = $filePath;
        $transfer->save();

        return response()->json([
            'path' => $filePath,
            'url'  => Storage::disk('oci')
                        ->temporaryUrl($filePath, now()->addMinutes(10)),
        ]);
    }

    /**
     * Secure download (Alternative to temporaryUrl if needed)
     */
    public function download($guard, $filePath)
    {
        if (!auth()->guard($guard)->check()) {
            abort(401);
        }

        if (!Storage::disk('oci')->exists($filePath)) {
            abort(404);
        }

        $url = Storage::disk('oci')
            ->temporaryUrl($filePath, now()->addMinutes(5));

        return redirect()->away($url);
    }
}
