<?php

namespace App\Nova\Actions;

use App\Services\TransferService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;

class TranferTransactionsExcelDownload extends Action
{
    /**
     * Display name
     */
    public function name()
    {
        return __('Download Transfer Transactions Excel');
    }

    /**
     * Execute action
     */
    public function handle(ActionFields $fields, Collection $models)
    {
        $transfer = $models->first();

        if (!$transfer) {
            return Action::danger('Transfer not found.');
        }

        $files = $transfer->filters['files'] ?? [];

        if (!isset($files['file_path'])) {
            return Action::danger('No file attached to this transfer.');
        }

        $filePath = $files['file_path'];
        $fileName = $files['file_name'] ?? basename($filePath);

        if (!Storage::disk('oci')->exists($filePath)) {
            return Action::danger('File not found on storage.');
        }

        $stream = Storage::disk('oci')->readStream($filePath);

        if (!$stream) {
            return Action::danger('Unable to read file stream.');
        }

        return response()->streamDownload(function () use ($stream) {
            fpassthru($stream);
            fclose($stream);
        }, $fileName, [
            'Content-Type' => Storage::disk('oci')->mimeType($filePath) ?? 'application/octet-stream',
        ]);
    }

    /**
     * No extra fields required
     */
    public function fields()
    {
        return [];
    }

    /**
     * Optional: regenerate file if needed
     */
    public function updateFile($model)
    {
        if (!isset($model->filters['files'])) {
            return;
        }

        $folder = $model->filters['files']['folder'] ?? null;
        $transactionsName = $model->filters['files']['transactions'] ?? null;

        if (!$folder || !$transactionsName) {
            return;
        }

        $filename = 'bills/' . $folder . '/' . str_replace('transactions-', '', $transactionsName);

        TransferService::createTransactionsExcel(
            $model->transactions->load('bill'),
            $filename
        );
    }
}
