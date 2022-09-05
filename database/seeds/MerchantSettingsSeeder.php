<?php

namespace Database\Seeders;

use App\Models\MerchantSetting;
use App\Models\User;
use Illuminate\Database\Seeder;

class MerchantSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $merchants = User::all();
        $settings = config('merchant_settings');

        foreach($merchants as $merchant){
            foreach($settings as $key => $setting){
                $merchantSettings = MerchantSetting::firstOrCreate(
                    ['user_id' => $merchant->id, 'key' =>  $key],
                    ['key' => $key, 'value' => $setting]
                );
            }

        }

        MerchantSetting::whereNotIn('key', array_keys($settings))->delete();

        dd('success');
    }
}