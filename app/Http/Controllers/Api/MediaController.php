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
      return response()->json(['error' => $validator->errors()], 422);
    }

    if (!$request->hasFile('file')) {
      return response()->json(['error' => 'No file uploaded'], 400);
    }

    $file = $request->file('file');
    $name = time().'-'.uniqid().'.'.$file->getClientOriginalExtension();

    $folder = $request->folder ? trim($request->folder, '/') : '';
    $path = Storage::disk('oci')->putFileAs($folder, $file, $name);

    return response()->json(['data' => $path]);
  }

  /**
   * change Status.
   *
   * @param  \App\Models\Transfer  $Transfer
   * @return \Illuminate\Http\Response
   */
  public function uploadAttachment(Request $request, Transfer $transfer)
  {
    $validator = Validator::make($request->all(), [
      'file' => ['required', new ValidateUploadFile(['pdf', 'png', 'jpeg', 'jpg', 'docx', 'doc', 'xlsx', 'csv'])],
    ]);

    if ($validator->fails()){
      return response()->json(['error' => $validator->errors()], 422);
    }

    if (!$request->hasFile('file')) {
      return response()->json(['error' => 'No file uploaded'], 400);
    }

    $file = $request->file('file');
    $name = time().'-'.uniqid().'.'.$file->getClientOriginalExtension();

    $path = Storage::disk('oci')->putFileAs('attachments', $file, $name);

    $transfer->attachment = $path;
    $transfer->save();

    return response()->json([
      'data' => [
        'path' => $path,
        'url' => Storage::disk('oci')->url($path)
      ]
    ]);
  }

  public function getFile($guard ,$fileName)
  {
    if(auth()->guard($guard)->check())
    {
      if (Storage::disk('oci')->exists($fileName)){
        return Storage::disk('oci')->download($fileName);
      }
      abort(404);
    }
    return abort(401);
  }
}
