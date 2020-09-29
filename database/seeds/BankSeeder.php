<?php

use App\Bank;
use Illuminate\Database\Seeder;

class BankSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (getBanks() as $item) {
        	$bank = new Bank;
        	$bank->code = $item['id'];
        	$bank->setTranslation('name', 'en', $item['en']);
        	$bank->setTranslation('name', 'ar', $item['ar']);
        	$bank->save();
        }
    }
}
