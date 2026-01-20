<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ClearDownloadsFolder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * Example usage: php artisan downloads:clear
     */
    protected $signature = 'downloads:clear';

    /**
     * The console command description.
     */
    protected $description = 'Delete all files and subdirectories under storage/app/downloads every hour';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $path = 'downloads';
        Storage::disk('public')->deleteDirectory($path);
        $this->info("✅ Folder 'storage/app/{$path}' has been cleared successfully.");
    }
}
