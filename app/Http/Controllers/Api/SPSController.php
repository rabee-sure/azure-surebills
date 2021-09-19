<?php

namespace App\Http\Controllers\Api;

use App\Models\Transfer;
use Illuminate\Http\Request;
use App\Services\TransferService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class SPSController extends Controller
{   
    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Transfer  $transfer
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function transferStatement(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'transfers' => ['array', 'min:1'],
            'transfers.*.ReferenceNumber' => ['required', 'exists:settlements,id'],
            'transfers.*.StatusCode' => ['required'],
            'transfers.*.IsSuccess' => ['required', 'bool'],
            'transfers.*.Message' => ['required'],
            'transfers.*.TransferStatus' => ['required', 'in:Not Transferred,TransferSucceed,TranferFailed'],
        ]);


        if ($validator->fails()){
             return response()->json([
                'StatusCode' => '422',
                'IsSuccess' => false,
                'Message' => $validator->errors()->first(),
            ]);
        }
        
        foreach($request->transfers as $data){
            $transfer = Transfer::find($data['ReferenceNumber']);
            if($transfer){
                $lookups = [
                    'Not Transferred' => 'pending',
                    'TransferSucceed' => 'completed',
                    'TranferFailed' => 'failed',
                ];
                $status = $lookups[$data['TransferStatus']];
                TransferService::changeTranferStatus($transfer,  $status, null, $data, true);
            }
        }

        return response()->json([
            'StatusCode' => '200',
            'IsSuccess' => true,
            'Message' => 'Success',
        ]);
    }


}
