<?php

return [
    'forward_webhooks' => env('MASTERCARD_FORWARD_WEBHOOKS'),
    'received_webhook' => env('MASTERCARD_RECEIVED_WEBHOOK'),
    'webhook_simulation' => env('MASTERCARD_WEBHOOK_SIMULATION', false),
    'webhook_simulation_delay_in_minutes' => env('MASTERCARD_WEBHOOK_SIMULATION_DELAY_IN_MINUTES', 1),
    // Full payment-cycle simulation (no real MPGS calls).
    // Enabled only in non-production environments via env:
    // MASTERCARD_PAYMENT_SIMULATION=true
    'payment_simulation' => env('MASTERCARD_PAYMENT_SIMULATION', false),
];