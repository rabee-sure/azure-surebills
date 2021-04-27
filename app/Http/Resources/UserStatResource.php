<?php

namespace App\Http\Resources;

use App\Models\Bill;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class UserStatResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'balance' => round($this->balance, 2),
            'business_name' => $this->business_name,
            'email' => $this->email,
            'mobile' => $this->mobile,
            'mobile_sent_at' => $this->mobile_sent_at->timestamp ?? null,
            'name' => $this->name,
            'stats' => $this->getStats(),
        ];
    }


    protected function getStats()
    {
        $total_paid = $this->bills()->paid()->sum('total');
        $total_bills = $this->bills()->count();
        $total_paid_bills = $this->bills()->paid()->count();

        return [
            'balance' =>  round($this->balance, 2),
            'total_paid' =>  round($total_paid, 2),
            'total_bills' =>  $total_bills,
            'total_paid_bills' =>  $total_paid_bills,
            'filter_user_id' =>  base64_encode(
                json_encode([
                    [
                        "class" => "App\Nova\Filters\UserId",
                        "value" => $this->id,
                    ]
                ])
            ),
            'filter_user_id_and_bill_status_paid' =>  base64_encode(
                json_encode([
                    [
                        "class" => "App\Nova\Filters\UserId",
                        "value" => $this->id,
                    ],
                    [
                        "class" => "App\Nova\Filters\BillStatus",
                        "value" => ["paid"]
                    ]
                ])
            ),
        ];
    }
}
