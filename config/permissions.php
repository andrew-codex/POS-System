<?php

return [
    'roles' => [
        'admin' => [
            'view_dashboard',
            'manage_products',
            'view_products',
            'view_categories',
            'view_stock',
            'view_cart',
            'view_sales',
            'view_reports',
            'manage_settings',
        ],
        'cashier' => [
            'view_dashboard',
            'view_cart',
            'view_sales',
        ],
        'manager' => [
            'view_dashboard',
            'view_sales',
            'view_reports',
            'view_products',
            'view_categories',
            'view_stock',
        ],
    ],
];
