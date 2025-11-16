<?php

return [
    'forward_webhooks' => env('MASTERCARD_FORWARD_WEBHOOKS'),
    'received_webhook' => env('MASTERCARD_RECEIVED_WEBHOOK'),
    'webhook_simulation' => env('MASTERCARD_WEBHOOK_SIMULATION', false),
    'webhook_simulation_delay_in_minutes' => env('MASTERCARD_WEBHOOK_SIMULATION_DELAY_IN_MINUTES', 1),
];