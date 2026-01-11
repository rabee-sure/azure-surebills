<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Coupon\CouponService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

/**
 * CouponController (API)
 * 
 * Handles API routes for coupon operations.
 * Service-first architecture - all business logic in CouponService.
 */
class CouponController extends Controller
{
    protected $couponService;

    public function __construct(CouponService $couponService)
    {
        $this->couponService = $couponService;
    }

    /**
     * Validate and apply a coupon code
     * 
     * Returns discount information if valid:
     * { discount_type: 'fixed'|'percentage', discount_value: number }
     */
    public function validateCoupon(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'code' => 'required|string',
            'customer_id' => 'required|integer|exists:customers,id',
            'bill_id' => 'nullable|integer|exists:bills,id',
        ]);

        $userId = Auth::user()->store_main_user_id ?? Auth::user()->id;

        $result = $this->couponService->validateAndApply(
            $validated['code'],
            $validated['customer_id'],
            $userId,
            $validated['bill_id'] ?? null
        );

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message'],
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => $result['message'],
            'discount' => $result['discount'],
        ]);
    }

    /**
     * Get all coupons for authenticated merchant
     */
    public function index(Request $request): JsonResponse
    {
        $userId = Auth::user()->store_main_user_id ?? Auth::user()->id;
        $perPage = $request->get('per_page', 15);

        $coupons = $this->couponService->getCoupons($userId, $perPage);

        return response()->json([
            'success' => true,
            'data' => $coupons,
        ]);
    }

    /**
     * Get a single coupon
     */
    public function show(int $id): JsonResponse
    {
        $userId = Auth::user()->store_main_user_id ?? Auth::user()->id;
        $coupon = $this->couponService->getCoupon($id, $userId);

        if (!$coupon) {
            return response()->json([
                'success' => false,
                'message' => __('Coupon not found'),
            ], 404);
        }

        $stats = $this->couponService->getUsageStats($id, $userId);

        return response()->json([
            'success' => true,
            'data' => [
                'coupon' => $coupon,
                'stats' => $stats,
            ],
        ]);
    }
}
