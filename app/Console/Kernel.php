<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $transactionCheckerInterval = config('cybersource.transaction_checker_command_interval');
        $schedule->command('expire:bill')->everyMinute();
        $schedule->command('delete:uncompleted')->daily();
        $schedule->command('transfer:automatic')->daily();
        $schedule->command('merchants:transfer_balance')->dailyAt('03:00');
        $schedule->command('bills:check_paid_bills_missing_transactions')->dailyAt('03:00');
        $schedule->command('admin:report_inactive')->quarterly();
        $schedule->command('admin:block_password')->quarterly();
        if($transactionCheckerInterval && config('cybersource.transaction_checker_active')){
            $schedule->command('cybersource:get-transaction-details')->cron($transactionCheckerInterval);
        }
        if(config('mastercard.webhook_simulation')){
            $schedule->command('fix:mastercard-payment-log', [today()->format('Y-m-d')  , today()->format('Y-m-d')])->everyMinute();
        }
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
