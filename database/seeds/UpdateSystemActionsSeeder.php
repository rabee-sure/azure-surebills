<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateSystemActionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('system_actions')->where('action_name', 'user_add_decouments')->update(['action_name' => 'user_add_documents']);
        DB::table('system_actions')->where('action_name', 'user_delete_decouments')->update(['action_name' => 'user_delete_documents']);
    }
}
