<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Services\Coupon\CouponService;
use App\Enums\CouponMechanism;
<<<<<<< HEAD
use App\Http\Requests\CouponBulkGenerationCodesExportRequest;
use App\Http\Requests\CouponBulkGenerationCodesStoreRequest;
use App\Http\Requests\CouponStoreRequest;
=======
>>>>>>> 79152f3b8ca19cc1464254750d139cfac6ccb9f4
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * CouponController (Web)
 *
 * Handles web routes for coupon management.
 * Uses CouponService for all business logic.
 */
class CouponController extends Controller
{
    protected $couponService;

    public function __construct(CouponService $couponService)
    {
        $this->couponService = $couponService;
        // Add middleware for permissions if needed
        // $this->middleware('permission:show coupons', ['only' => ['index', 'show']]);
        // $this->middleware('permission:create coupons', ['only' => ['create', 'store']]);
    }

    /**
     * Display a listing of coupons
     */
    public function index(Request $request)
    {
        $userId = Auth::user()->store_main_user_id ?? Auth::user()->id;
        $perPage = $request->get('per_page', 15);

        $coupons = $this->couponService->getCoupons($userId, $perPage);

        // Add additional data for views
        foreach ($coupons as $coupon) {
            $coupon->stats = $this->couponService->getUsageStats($coupon->id, $userId);
        }

        return view('coupons.index', [
            'coupons' => $coupons,
        ]);
    }

    /**
     * Show the form for creating a new coupon
     */
    public function create()
    {
        $mechanisms = CouponMechanism::options();

        return view('coupons.create', [
            'mechanisms' => $mechanisms,
            'coupon' => null,
        ]);
    }

    /**
     * Store a newly created coupon
     */
<<<<<<< HEAD
    public function store(CouponStoreRequest $request)
    {
        $validated = $request->validated();
=======
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'mechanism' => 'required|in:' . implode(',', CouponMechanism::values()),
            'discount_type' => 'required|in:fixed,percentage',
            'discount_value' => 'required|numeric|min:0' . ($request->discount_type === 'percentage' ? '|max:100' : ''),
            'valid_from' => 'nullable|date',
            'valid_to' => 'nullable|date|after_or_equal:valid_from',
            'max_usage' => 'nullable|integer|min:1',
            'max_customer_usage' => 'nullable|integer|min:1',
            'code_pattern' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);
>>>>>>> 79152f3b8ca19cc1464254750d139cfac6ccb9f4

        $userId = Auth::user()->store_main_user_id ?? Auth::user()->id;

        // Set mechanism-specific defaults
        if ($validated['mechanism'] === 'max_usage' || $validated['mechanism'] === 'max_customer_usage') {
            if (empty($validated['code_pattern'])) {
                $validated['code_pattern'] = strtoupper(\Illuminate\Support\Str::random(8));
            }
        }

        if ($validated['mechanism'] === 'one_time_usage') {
            // For one-time usage, code_pattern is used for bulk generation
            $validated['code_pattern'] = $validated['code_pattern'] ?? '{RANDOM:8}';
        }

        $coupon = $this->couponService->createCoupon($validated, $userId);

        return redirect()
            ->route('coupons.show', $coupon->id)
            ->with('success', __('Coupon created successfully'));
    }

    /**
     * Display the specified coupon
     */
    public function show(int $id)
    {
        $userId = Auth::user()->store_main_user_id ?? Auth::user()->id;
        $coupon = $this->couponService->getCoupon($id, $userId);

        if (!$coupon) {
            abort(404);
        }

        $stats = $this->couponService->getUsageStats($id, $userId);

        // Get recent codes for one-time usage coupons
        $codes = null;
        if ($coupon->mechanism && $coupon->mechanism->equals(CouponMechanism::ONE_TIME_USAGE)) {
            $codes = $coupon->codes()->orderBy('created_at', 'desc')->limit(50)->get();
        }

        return view('coupons.show', [
            'coupon' => $coupon,
            'stats' => $stats,
            'codes' => $codes,
        ]);
    }

    /**
     * Show bulk generation form
     */
    public function bulkGenerate(int $id)
    {
        $userId = Auth::user()->store_main_user_id ?? Auth::user()->id;
        $coupon = $this->couponService->getCoupon($id, $userId);

        if (!$coupon) {
            abort(404);
        }

        if (!$coupon->mechanism || !$coupon->mechanism->equals(CouponMechanism::ONE_TIME_USAGE)) {
            return redirect()
                ->route('coupons.show', $id)
                ->with('error', __('Bulk generation is only available for one-time usage coupons'));
        }

        return view('coupons.bulk-generate', [
            'coupon' => $coupon,
        ]);
    }

    /**
     * Process bulk generation
     */
<<<<<<< HEAD
    public function storeBulkGenerate(CouponBulkGenerationCodesStoreRequest $request, int $id)
    {
        $validated = $request->validated();
=======
    public function storeBulkGenerate(Request $request, int $id)
    {
        $validated = $request->validate([
            'count' => 'required|integer|min:1|max:10000',
            'pattern' => 'nullable|string|max:255',
        ]);
>>>>>>> 79152f3b8ca19cc1464254750d139cfac6ccb9f4

        $userId = Auth::user()->store_main_user_id ?? Auth::user()->id;
        $coupon = $this->couponService->getCoupon($id, $userId);

        if (!$coupon) {
            abort(404);
        }

        $result = $this->couponService->generateBulkCodes(
            $coupon,
            $validated['count'],
            $validated['pattern'] ?? null
        );

        if (!$result['success']) {
            return back()
                ->withInput()
                ->withErrors(['error' => $result['message']]);
        }

        return redirect()
            ->route('coupons.show', $id)
            ->with('success', $result['message']);
    }

    /**
     * Export coupon codes
     */
<<<<<<< HEAD
    public function export(CouponBulkGenerationCodesExportRequest $request, int $id)
    {
        $validated = $request->validated();
=======
    public function export(Request $request, int $id)
    {
        $validated = $request->validate([
            'format' => 'required|in:csv,excel',
        ]);
>>>>>>> 79152f3b8ca19cc1464254750d139cfac6ccb9f4

        $userId = Auth::user()->store_main_user_id ?? Auth::user()->id;
        $coupon = $this->couponService->getCoupon($id, $userId);

        if (!$coupon) {
            abort(404);
        }

        $result = $this->couponService->exportCodes($coupon, $validated['format']);

        // If result is a response (success), return it
        if ($result instanceof \Illuminate\Http\Response
            || $result instanceof \Symfony\Component\HttpFoundation\BinaryFileResponse
            || $result instanceof \Symfony\Component\HttpFoundation\StreamedResponse) {
            return $result;
        }

        // Otherwise it's an error array
        return back()->withErrors(['error' => $result['message'] ?? __('Export failed')]);
    }

    /**
     * Show export form
     */
    public function showExport(int $id)
    {
        $userId = Auth::user()->store_main_user_id ?? Auth::user()->id;
        $coupon = $this->couponService->getCoupon($id, $userId);

        if (!$coupon) {
            abort(404);
        }

        if (!$coupon->mechanism || !$coupon->mechanism->equals(CouponMechanism::ONE_TIME_USAGE)) {
            return redirect()
                ->route('coupons.show', $id)
                ->with('error', __('Export is only available for one-time usage coupons'));
        }

        return view('coupons.export', [
            'coupon' => $coupon,
        ]);
    }
<<<<<<< HEAD

    /**
     * Toggle coupon active/inactive status.
     */
    public function toggleStatus(int $id)
    {
        $userId = Auth::user()->store_main_user_id ?? Auth::user()->id;
        $coupon = $this->couponService->getCoupon($id, $userId);

        if (!$coupon) {
            abort(404);
        }

        $result = $this->couponService->toggleStatus($coupon);

        if (!$result['success']) {
            return back()->withErrors(['error' => $result['message']]);
        }

        return back()->with('success', $result['message']);
    }

    /**
     * Delete coupon if not used yet.
     */
    public function destroy(int $id)
    {
        $userId = Auth::user()->store_main_user_id ?? Auth::user()->id;
        $coupon = $this->couponService->getCoupon($id, $userId);

        if (!$coupon) {
            abort(404);
        }

        $result = $this->couponService->deleteCouponIfUnused($coupon);

        if (!$result['success']) {
            return back()->withErrors(['error' => $result['message']]);
        }

        return redirect()->route('coupons.index')->with('success', $result['message']);
    }
=======
>>>>>>> 79152f3b8ca19cc1464254750d139cfac6ccb9f4
}
