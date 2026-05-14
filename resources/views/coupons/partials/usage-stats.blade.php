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

<div class="row g-6">

  <div class="col-md-6">
    <div class="card h-100">
      <div class="card-body p-3">
        <h6 class="card-title text-capitalize">{{ __('Total Usage') }}</h6>
        <h4 class="card-text text-danger fw-bold my-3 lh-1">{{ number_format($totalUsage) }}</h4>
        @if($uniqueCustomers > 0)
          <small class="card-text mb-0">{{ __('by :count customers', ['count' => $uniqueCustomers]) }}</small>
        @endif
      </div><!-- card-body -->
    </div><!-- card -->
  </div><!-- col -->

  @if($remaining !== null)
    <div class="col-md-6">
      <div class="card h-100">
        <div class="card-body p-3">
          <h6 class="card-title text-capitalize">{{ __('Remaining') }}</h6>
          <h4 class="card-text text-success fw-bold my-3 lh-1">{{ number_format($remaining) }}</h4>
          @if($limit)
            <small class="card-text mb-0">{{ __('of :limit total', ['limit' => number_format($limit)]) }}</small>
          @endif
        </div><!-- card-body -->
      </div><!-- card -->
    </div><!-- col -->
  @endif

  @if($limit && $coupon->mechanism && $coupon->mechanism->value() === 'max_usage')
    <div class="col-md-12">
      <div class="card h-100">
        <div class="card-body p-3">
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
        </div><!-- card-body -->
      </div><!-- card -->
    </div><!-- col -->
  @endif

  @if($coupon->mechanism && $coupon->mechanism->value() === 'one_time_usage')
    @php
      $totalCodes = $stats['total_codes'] ?? $coupon->codes->count() ?? 0;
      $usedCodes = $stats['used_codes'] ?? $coupon->codes->where('is_used', true)->count() ?? 0;
    @endphp
    <div class="col-md-12">
      <div class="card h-100">
        <div class="card-body p-3 d-flex flex-column gap-3">
          <div class="d-flex align-items-center justify-content-between">
            <span class="d-block text-capitalize">{{ __('Codes Status') }}</span>
            <small class="d-block text-muted">{{ __(':used used of :total', ['used' => $usedCodes, 'total' => $totalCodes]) }}</small>
          </div>
          @if($totalCodes > 0)
            <div class="progress flex-grow-1 w-100" style="height: 10px;">
              <div
                class="progress-bar progress-bar-striped progress-bar-animated bg-success"
                role="progressbar"
                style="width: {{ ($usedCodes / $totalCodes) * 100 }}%"
                aria-valuenow="{{ $usedCodes }}"
                aria-valuemin="0"
                aria-valuemax="{{ $totalCodes }}"
              >
              </div>
            </div>
          @endif
        </div><!-- card-body -->
      </div><!-- card -->
    </div><!-- col -->
  @endif

</div><!-- row -->