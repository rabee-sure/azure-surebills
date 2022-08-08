<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ActionLog;

class DeleteActionsLogsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ActionLog::query()->delete();

    }
}
