<?php

namespace App\Console\Commands;

use App\Exports\TransactionsExport;
use App\Events\TransferFileGenerated;
use App\Http\Resources\TransactionExportResource;
use App\Models\Transfer;
use App\Services\BasicSettingsService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class TransferExcel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transfer:excel {id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'create transfer excel sheet.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(BasicSettingsService $basicSettingsService)
    {
        $transfer_emails = $basicSettingsService->get('transfer_emails', '');

        $cycleDate = Carbon::now()->addHours(3);

        $transfer = Transfer::findOrFail($this->argument('id'));
        $file_name = $transfer->filters['files']['file_path'];

        $data = json_decode((TransactionExportResource::collection($transfer->transactions->load('bill.application.channel')))->toJson(), true);

        if (! Excel::store(new TransactionsExport($data), $file_name, 'public')) {
            return 1;
        }

        $transfer->addMediaFromDisk($file_name, 'public')
            ->preservingOriginal()
            ->toMediaCollection('transfers_transactions');

        event(new TransferFileGenerated($transfer_emails, $cycleDate, $transfer));

        return 0;
    }
}
