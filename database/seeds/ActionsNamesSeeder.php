<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SystemAction;

class ActionsNamesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach(config('actions_names.actions') as $action)
        {
            SystemAction::firstOrCreate(['action_name' => $action], ['action_name' => $action]);
        }
    }
}
