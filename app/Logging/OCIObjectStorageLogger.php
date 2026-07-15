<?php

namespace App\Logging;

use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;
use Illuminate\Filesystem\FilesystemManager;

class OCIObjectStorageLogger
{
    public function __invoke(array $config)
    {
        $logger = new Logger('oci');

        $logger->pushHandler(new class(app(FilesystemManager::class)) extends AbstractProcessingHandler {
            protected $filesystem;

            public function __construct($filesystem)
            {
                parent::__construct();
                $this->filesystem = $filesystem;
            }

            protected function write(array $record): void
            {
                $line = '[' . $record['datetime']->format('Y-m-d H:i:s') . '] ' . $record['message'] . PHP_EOL;
                $path = 'logs/' . now()->format('Y-m-d') . '.log';

                if ($this->filesystem->disk('oci')->exists($path)) {
                    $existing = $this->filesystem->disk('oci')->get($path);
                    $this->filesystem->disk('oci')->put($path, $existing . $line);
                } else {
                    $this->filesystem->disk('oci')->put($path, $line);
                }
            }
        });

        return $logger;
    }
}
