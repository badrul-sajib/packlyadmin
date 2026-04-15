<?php

return [

    'rules' => [
        'velocity' => [
            'enabled' => true,
            'max_failed_attempts' => 3,
            'failed_attempt_window' => 10, // minutes
            'max_cod_orders' => 5,
            'cod_order_window' => 24, // hours
            'max_same_address' => 3,
            'address_window' => 24, // hours
        ],
       
        'order_value_outlier' => [
            'enabled' => true,
            'max_normal_order' => 1000,
            'outlier_multiplier' => 3,
        ],
        'cancelled_abuse' => [
            'enabled' => true,
            'max_rate' => 0.5,
            'min_orders_for_rate' => 5,
            'max_recent_refunds' => 3,
            'cancelled_window' => 30, // days
        ],
        'refund_abuse' => [
            'enabled' => true,
            'max_rate' => 0.5,
            'min_orders_for_rate' => 5,
            'max_recent_refunds' => 3,
            'refund_window' => 30, // days
        ],
    ],
    'admin_email' => 'shop@test.com',
    'max_normal_order' => 900000,

];
