{{-- Mechanism Badge Partial --}}
{{-- Displays a badge indicating the coupon mechanism type --}}

@php
    $coupon = $coupon ?? null;
    $mechanism = $mechanism ?? ($coupon ? $coupon->mechanism : null);
    
    if (!$mechanism) {
        return;
    }
    
    $badgeClasses = [
        'max_usage' => 'bg-primary',
        'max_customer_usage' => 'bg-info',
        'one_time_usage' => 'bg-success',
    ];
    
    $badgeClass = $badgeClasses[$mechanism->value()] ?? 'bg-secondary';
@endphp

<span class="badge {{ $badgeClass }} text-white px-3 py-2 rounded-pill">
    {{ $mechanism->label() }}
</span>
