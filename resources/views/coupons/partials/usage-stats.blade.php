{{-- Usage Stats Partial --}}
{{-- Displays usage statistics for a coupon --}}

@php
    $stats = $stats ?? [];
    $coupon = $coupon ?? null;

    if (!$coupon) {
        return;
    }

    $totalUsage = $stats['total_usage'] ?? $coupon->total_usage ?? 0;
    $remaining = $stats['remaining'] ?? $coupon->remaining_usage ?? null;
    $limit = $stats['limit'] ?? null;
    $uniqueCustomers = $stats['unique_customers'] ?? 0;
@endphp

<div class="usage-stats">
  <div class="row g-3">

    <div class="col-md-6">
      <div class="border shadow-sm rounded-2 p-3 d-flex flex-column gap-2 h-100">
        <span class="d-block text-capitalize">{{ __('Total Usage') }}</span>
        <span class="d-block fs-5 fw-bold text-danger">{{ number_format($totalUsage) }}</span>
        @if($uniqueCustomers > 0)
          <small class="text-muted">{{ __('by :count customers', ['count' => $uniqueCustomers]) }}</small>
        @endif
      </div>
    </div><!-- col -->

    @if($remaining !== null)
      <div class="col-md-6">
        <div class="border shadow-sm rounded-2 p-3 d-flex flex-column gap-2 h-100">
          <span class="d-block text-capitalize">{{ __('Remaining') }}</span>
          <span class="d-block fs-5 fw-bold text-success">{{ number_format($remaining) }}</span>
          @if($limit)
            <small class="text-muted">{{ __('of :limit total', ['limit' => number_format($limit)]) }}</small>
          @endif
        </div>
      </div><!-- col -->
    @endif

    @if($limit && $coupon->mechanism && $coupon->mechanism->value() === 'max_usage')
      <div class="col-md-12">
        <div class="d-flex flex-column gap-2">
          <div class="d-flex align-items-center justify-content-between">
            <span class="d-block text-capitalize">{{ __('Usage Progress') }}</span>
            <small class="d-block text-muted">{{ number_format(($totalUsage / $limit) * 100, 1) }}%</small>
          </div>
          <div class="progress" style="height: 10px;">
            <div
              class="progress-bar bg-primary"
              role="progressbar"
              style="width: {{ min(100, ($totalUsage / $limit) * 100) }}%"
              aria-valuenow="{{ $totalUsage }}"
              aria-valuemin="0"
              aria-valuemax="{{ $limit }}"
            >
            </div>
          </div>
        </div>
      </div><!-- col -->
    @endif

    @if($coupon->mechanism && $coupon->mechanism->value() === 'one_time_usage')
      @php
        $totalCodes = $stats['total_codes'] ?? $coupon->codes->count() ?? 0;
        $usedCodes = $stats['used_codes'] ?? $coupon->codes->where('is_used', true)->count() ?? 0;
      @endphp
      <div class="col-md-12">
        <div class="d-flex flex-column gap-2 border shadow-sm rounded-2 p-3">
          <div class="d-flex align-items-center justify-content-between">
            <span class="d-block text-capitalize">{{ __('Codes Status') }}</span>
            <small class="d-block text-muted">{{ __(':used used of :total', ['used' => $usedCodes, 'total' => $totalCodes]) }}</small>
          </div>
          @if($totalCodes > 0)
            <div class="progress" style="height: 10px;">
              <div
                class="progress-bar bg-success"
                role="progressbar"
                style="width: {{ ($usedCodes / $totalCodes) * 100 }}%"
                aria-valuenow="{{ $usedCodes }}"
                aria-valuemin="0"
                aria-valuemax="{{ $totalCodes }}"
              >
              </div>
            </div>
          @endif
        </div>
      </div><!-- col -->
    @endif

  </div><!-- row -->



</div>
