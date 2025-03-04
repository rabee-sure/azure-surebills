<?php

namespace App\Traits;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

trait ExceptionTrait{

    /**
     * Building log exception
     * @param $data
     * @param int $code
     * @return \Illuminate\Http\JsonResponse
     */
    protected function logException($channel, $row, $exception)
    {
        Log::channel($channel)->info(json_encode($row).'\n'.$exception->getMessage().' at line: '.$exception->getLine().' at class: '.__CLASS__);
    }
}
