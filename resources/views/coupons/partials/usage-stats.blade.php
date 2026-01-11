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
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h6 class="text-muted mb-2">{{ __('Total Usage') }}</h6>
                    <h3 class="mb-0">{{ number_format($totalUsage) }}</h3>
                    @if($uniqueCustomers > 0)
                        <small class="text-muted">{{ __('by :count customers', ['count' => $uniqueCustomers]) }}</small>
                    @endif
                </div>
            </div>
        </div>
        
        @if($remaining !== null)
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h6 class="text-muted mb-2">{{ __('Remaining') }}</h6>
                    <h3 class="mb-0">{{ number_format($remaining) }}</h3>
                    @if($limit)
                        <small class="text-muted">{{ __('of :limit total', ['limit' => number_format($limit)]) }}</small>
                    @endif
                </div>
            </div>
        </div>
        @endif
    </div>
    
    @if($limit && $coupon->mechanism && $coupon->mechanism->value() === 'max_usage')
        <div class="mb-4">
            <div class="d-flex justify-content-between mb-2">
                <span class="text-muted">{{ __('Usage Progress') }}</span>
                <span class="text-muted">{{ number_format(($totalUsage / $limit) * 100, 1) }}%</span>
            </div>
            <div class="progress" style="height: 10px;">
                <div class="progress-bar bg-primary" role="progressbar" 
                     style="width: {{ min(100, ($totalUsage / $limit) * 100) }}%"
                     aria-valuenow="{{ $totalUsage }}" 
                     aria-valuemin="0" 
                     aria-valuemax="{{ $limit }}">
                </div>
            </div>
        </div>
    @endif
    
    @if($coupon->mechanism && $coupon->mechanism->value() === 'one_time_usage')
        @php
            $totalCodes = $stats['total_codes'] ?? $coupon->codes->count() ?? 0;
            $usedCodes = $stats['used_codes'] ?? $coupon->codes->where('is_used', true)->count() ?? 0;
        @endphp
        <div class="mb-4">
            <div class="d-flex justify-content-between mb-2">
                <span class="text-muted">{{ __('Codes Status') }}</span>
                <span class="text-muted">{{ __(':used used of :total', ['used' => $usedCodes, 'total' => $totalCodes]) }}</span>
            </div>
            @if($totalCodes > 0)
            <div class="progress" style="height: 10px;">
                <div class="progress-bar bg-success" role="progressbar" 
                     style="width: {{ ($usedCodes / $totalCodes) * 100 }}%"
                     aria-valuenow="{{ $usedCodes }}" 
                     aria-valuemin="0" 
                     aria-valuemax="{{ $totalCodes }}">
                </div>
            </div>
            @endif
        </div>
    @endif
</div>
