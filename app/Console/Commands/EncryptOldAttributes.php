<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
class EncryptOldAttributes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'encrypt:old-attributes {--model=} {--attributes=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command for encrypting old attributes provide comma separated list of attributes and model name must add attrebutes before on protected $encrypted and use HasEncryptedAttributes trait on the model';
    
    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $model = $this->option('model');
        $attributes = $this->option('attributes');

        if(!$model || !$attributes) {
            $this->error('Model and attributes are required');
            return 1;
        }

        $attributes = explode(',', $attributes);

        foreach($attributes as $attribute) {
            $this->info("Encrypting $attribute for $model");
            // use namespace to get the model
            $modelClass = "App\\Models\\$model";
            $modelClass::whereNotNull($attribute)->chunk(100, function($models) use ($attribute) {
                foreach($models as $model) {
                    $model->$attribute = $model->$attribute;
                    $model->save();
                }
            });
        }

        return 0;
    }
}
