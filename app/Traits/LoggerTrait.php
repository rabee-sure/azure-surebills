<?php

namespace App\Traits;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

trait LoggerTrait{
    public function logResult($fileName, $result)
    {
        Log::build(['driver' => 'single', 'path' => storage_path('logs/' . $fileName . '.log'), 'level' => 'debug'])->error($result);
    }
}
