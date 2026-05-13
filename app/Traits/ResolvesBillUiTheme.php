<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Builds the merchant-customizable theme variables consumed by the bill
 * payment Blade views (`resources/views/bills/pay.blade.php` and
 * `resources/views/bills/payment_page.blade.php`).
 *
 * Centralizes settings lookup, default fallbacks,
 *
 * Used by both the web `App\Http\Controllers\BillController` and the API
 * `App\Http\Controllers\Api\BillController` because they render the same
 * Blade templates and therefore must inject the same `$billUiTheme` payload.
 */
trait ResolvesBillUiTheme
{
    /**
     * @param  \App\Models\Bill  $bill
     * @return array{bgColor:string,bgImageUrl:?string,textColor:string,btnBgColor:string,btnTextColor:string}
     */
    protected function resolveBillUiTheme($bill): array
    {
        $settings = $bill->user->settings;

        $bgImage = $settings->background_image_file ?? null;

        return [
            'bgColor'      => $settings->background_color_body ?? '#fafafa',
            'bgImage'      => $bgImage,
            'textColor'    => $settings->text_color_body ?? '#000000',
            'btnBgColor'   => $settings->background_color_payment_button ?? '#00d595',
            'btnTextColor' => $settings->text_color_payment_button ?? '#ffffff',
        ];
    }
}
