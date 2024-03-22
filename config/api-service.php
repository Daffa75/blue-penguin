<?php

// config for Rupadana/ApiService
return [
    'navigation' => [
        'group' => [
            'token' => 'Roles and Permissions',
        ],
    ],
    'models' => [
        'token' => [
            'enable_policy' => true,
        ],
    ],
    'route' => [
        'panel_prefix' => true,
        'use_resource_middlewares' => false,
    ],
    'tenancy' => [
        'enabled' => false,
        'awareness' => false,
    ]
];
