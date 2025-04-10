<?php

return [

    'merchant_id' => env('CYBERSOURCE_MERCHANT_ID'),

    'api_key' => env('CYBERSOURCE_API_KEY'),

    'secret_key' => env('CYBERSOURCE_SECRET_KEY'),

    'environment' => env('CYBERSOURCE_ENV', 'sandbox'), // 'sandbox' or 'production'
    
    'microform_enabled' => env('CYBERSOURCE_MICROFORM_ENABLED', false),
    
    'device_data_collection_action_url' => env('CYBERSOURCE_DEVICE_DATA_COLLECTION_ACTION_URL', null),
    'payer_auth_setup_url' => env('CYBERSOURCE_PAYER_AUTH_SETUP_URL', null),

    'allowed_card_networks' => ['AMEX', 'CARNET', 'CARTESBANCAIRES', 'CUP', 'DINERSCLUB', 'DISCOVER', 'EFTPOS', 'ELO', 'JCB', 'JCREW', 'MADA', 'MAESTRO', 'MASTERCARD', 'MEEZA', 'VISA'],
    
    'api_url' => [

        'sandbox' => 'https://apitest.cybersource.com',

        'production' => 'https://api.cybersource.com',

    ],

    'webhook_url' => env('CYBERSOURCE_WEBHOOK_URL'),
    'webhook_health_check_url' => env('CYBERSOURCE_HEALTH_CHECK_URL'),
    
    'transaction_checker_active' => env('CYBERSOURCE_TRANSACTION_CHECKER_ACTIVE'),
    'transaction_checker_command_interval' => env('CYBERSOURCE_TRANSACTION_CHECKER_COMMAND_INTERVAL'),
    'transaction_checker_period' => [
        'unit' => env('CYBERSOURCE_TRANSACTION_CHECKER_PERIOD_UNIT'),
        'value' => env('CYBERSOURCE_TRANSACTION_CHECKER_PERIOD_VALUE'),
    ],

    'products' => [
        [
            "productId" => "recurringBilling",
            "eventTypes"=> [
                "rbs.subscriptions.charge.created",
                "rbs.subscriptions.charge.pre-notified",
                "rbs.subscriptions.charge.failed",
            ]
        ],
        [
            "productId" => "alternativePaymentMethods",
            "eventTypes"=> [
                "payments.payments.updated"
            ]
        ],
        [
            "productId" => "cns",
            "eventTypes" => [
                "cns.outage.notify.freeform",
                "cns.report.keyExpiration.detail"
            ]
        ],
        [
            "productId" => "tokenManagement",
            "eventTypes" => [
                "tms.networktoken.provisioned",
                "tms.token.updated",
                "tms.token.pan_updated",
                "tms.token.created",
                "tms.networktoken.updated"
            ]
        ],
        [
            "productId" => "fraudManagementEssentials",
            "eventTypes" => [
                "risk.casemanagement.addnote",
                "risk.casemanagement.decision.accept",
                "risk.casemanagement.decision.reject",
                "risk.profile.decision.monitor",
                "risk.profile.decision.reject",
                "risk.profile.decision.review",
                "risk.profile.decision.review.5m",
                "risk.profile.decision.reject.5m",
                "risk.profile.decision.monitor.5m",
                "risk.profile.decision.review.5m",
                "risk.profile.decision.reject.5m",
                "risk.profile.decision.monitor.5m",
            ]
        ],
        [
            "productId" => "secureAcceptance",
            "eventTypes" => [
                "sa.orders.cardholderreceipts",
                "sa.orders.rawtransactionresults",
                "sa.orders.merchantreceipts",
                "sa.order.confirmation"
            ]
        ],
        [
            "productId" => "customerInvoicing",
            "eventTypes" => [
                "invoicing.customer.invoice.cancel",
                "invoicing.customer.invoice.send",
                "invoicing.customer.invoice.reminder",
                "invoicing.customer.invoice.partial-resend",
                "invoicing.customer.invoice.partial-payment",
                "invoicing.customer.invoice.paid",
                "invoicing.customer.invoice.overdue-reminder",
                "invoicing.merchant.invoice.send",
                "invoicing.merchant.invoice.paid",
                "invoicing.merchant.invoice.partial-paid",
                "invoicing.merchant.invoice.cancel",
                
            ]
        ],
        [
            "productId" => "terminalManagement",
            "eventTypes" => [
                "terminalManagement.assignment.update",
                "terminalManagement.status.update",
                "terminalManagement.reAssignment.update"
            ]
        ],
        [
            "productId" => "paymentOrchestration",
            "eventTypes" => []
        ],
        [
            "productId" => "unifiedCheckout",
            "eventTypes" => []
        ],
        [
            "productId" => "cardProcessing",
            "eventTypes" => [
                "payments.payments.accept",
                "payments.payments.review",
                "payments.payments.reject",
                "payments.payments.partial.approval",
                "payments.reversals.accept",
                "payments.reversals.reject",
                "payments.captures.accept",
                "payments.captures.review",
                "payments.captures.reject",
                "payments.refunds.accept",
                "payments.refunds.reject",
                "payments.refunds.partial.approval",
                "payments.credits.accept",
                "payments.credits.review",
                "payments.credits.reject",
                "payments.credits.partial.approval",
                "payments.voids.accept",
                "payments.voids.reject",
            ]
        ],
        [
            "productId" => "accountUpdater",
            "eventTypes" => [
                "aura.batch.status.update",
                "aura.batch.status.update",

            ]
        ],
        [
            "productId" => "transactionSearch",
            "eventTypes" => []
        ],
        [
            "productId" => "virtualTerminal",
            "eventTypes" => [
                "vt.order.receipt",
                "vt.followon.receipt",
                "vt.transactions.receipt",
                "vt.order.receipt",
                "vt.followon.receipt",
                "vt.order.receipt",
                "vt.followon.receipt"
            ]
        ],
        [
            "productId" => "tax",
            "eventTypes" => []
        ],
        [
            "productId" => "payerAuthentication",
            "eventTypes" => []
        ],
        [
            "productId" => "reporting",
            "eventTypes" => []
        ],
        [
            "productId" => "payByLink",
            "eventTypes" => []
        ],
    ],

    'switch_date' => env('CYBERSOURCE_SWITCH_DATE', '2025-02-28 02:15:00'),

];