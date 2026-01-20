{{-- Mechanism Badge Partial --}}
{{-- Displays a badge indicating the coupon mechanism type --}}

@php
    $coupon = $coupon ?? null;
    $mechanism = $mechanism ?? ($coupon ? $coupon->mechanism : null);

    if (!$mechanism) {
        return;
    }
@endphp

<span class="d-block text-capitalize">{{ $mechanism->label() }}</span>
