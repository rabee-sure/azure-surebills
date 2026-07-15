{{-- Mechanism Badge Partial --}}
{{-- Displays a badge indicating the coupon mechanism type --}}

@php
    $coupon = $coupon ?? null;
    $mechanism = $mechanism ?? ($coupon ? $coupon->mechanism : null);

    if (!$mechanism) {
        return;
    }
@endphp

<<<<<<< HEAD
{{ $mechanism->label() }}
=======
<span class="d-block text-capitalize">{{ $mechanism->label() }}</span>
>>>>>>> 79152f3b8ca19cc1464254750d139cfac6ccb9f4
