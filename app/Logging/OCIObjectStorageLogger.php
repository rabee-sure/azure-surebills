<?php

namespace App\Logging;

use Illuminate\Support\Facades\Storage;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;

class OCIObjectStorageLogger
{
    public function __invoke(array $config)
    {
        $logger = new Logger('oci');
        $logger->pushHandler(new class extends AbstractProcessingHandler {
            protected function write(array $record): void
            {
                $line = '[' . $record['datetime']->format('Y-m-d H:i:s') . '] ' . $record['message'] . PHP_EOL;
                $path = 'logs/' . now()->format('Y-m-d') . '.log';
                if (Storage::disk('oci')->exists($path)) {
                    $existing = Storage::disk('oci')->get($path);
                    Storage::disk('oci')->put($path, $existing . $line);
                } else {
                    Storage::disk('oci')->put($path, $line);
                }
            }
        });

        return $logger;
    }
}
