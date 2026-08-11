<?php

namespace Allam\Zatca;

use App\Models\ZatcaLog as ModelsZatcaLog;
use Illuminate\Support\Str;

/**
 * A class defines certificate parser
 */
class ZatcaLog
{
    public function __construct() {}

    public function responseLog($data)
    {
        $zatcaLog = ModelsZatcaLog::create([
            'uuid' => Str::uuid()->toString(),
            'payload' => $data['payload'],
            'api' => $data['api'],
            'response' => $data['response'],
            'response_code' => $data['response_code'],
            'reporting_status' => $data['reporting_status'],
            'clearance_status' => $data['clearance_status'],
            'disposition_message' => $data['disposition_message'],
            'status' => $data['status'],
            'qrSellert_status' => $data['qrSellert_status'],
            'qrBuyert_status' => $data['qrBuyert_status'],
            'parentable_id' => $data['parentable_id'],
            'parentable_type' => $data['model'],
        ]);

        return $zatcaLog;
    }
}
