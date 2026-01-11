<?php

namespace App\Services\Coupon;

use App\Models\Coupon;
use App\Repositories\CouponRepository;
use App\Exports\CouponCodesExport;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;

/**
 * CouponExporter Service
 * 
 * Handles export of coupon codes to CSV/Excel formats.
 */
class CouponExporter
{
    protected $repository;

    public function __construct(CouponRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Export coupon codes to CSV
     * 
     * @param Coupon $coupon
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function exportToCsv(Coupon $coupon)
    {
        $codes = $this->repository->getCodesForCoupon($coupon->id);
        
        $filename = 'coupon_' . $coupon->id . '_codes_' . now()->format('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($codes, $coupon) {
            $file = fopen('php://output', 'w');
            
            // BOM for UTF-8 Excel compatibility
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Headers
            fputcsv($file, ['Code', 'Status', 'Used At']);
            
            // Data rows
            foreach ($codes as $code) {
                fputcsv($file, [
                    $code->code,
                    $code->is_used ? __('Used') : __('Available'),
                    $code->is_used && $code->usage->first() 
                        ? $code->usage->first()->used_at->format('Y-m-d H:i:s')
                        : '',
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export coupon codes to Excel
     * 
     * @param Coupon $coupon
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportToExcel(Coupon $coupon)
    {
        $codes = $this->repository->getCodesForCoupon($coupon->id);
        
        $data = $codes->map(function($code) {
            return [
                'Code' => $code->code,
                'Status' => $code->is_used ? __('Used') : __('Available'),
                'Used At' => $code->is_used && $code->usage->first() 
                    ? $code->usage->first()->used_at->format('Y-m-d H:i:s')
                    : '',
            ];
        });

        $filename = 'coupon_' . $coupon->id . '_codes_' . now()->format('Y-m-d') . '.xlsx';
        
        return Excel::download(
            new CouponCodesExport($data),
            $filename
        );
    }

    /**
     * Get export data as array (for preview)
     */
    public function getExportData(Coupon $coupon, int $limit = 100): Collection
    {
        return $this->repository->getCodesForCoupon($coupon->id, $limit)
            ->map(function($code) {
                return [
                    'code' => $code->code,
                    'status' => $code->is_used ? __('Used') : __('Available'),
                    'used_at' => $code->is_used && $code->usage->first() 
                        ? $code->usage->first()->used_at->format('Y-m-d H:i:s')
                        : null,
                ];
            });
    }
}
