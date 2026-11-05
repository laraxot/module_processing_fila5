<?php

declare(strict_types=1);

return [
    'fields' => [
        'kind' => [
            'label' => 'Output kind',
            'placeholder' => '',
            'helper_text' => '',
            'tooltip' => '',
        ],
        'warehouse_id' => [
            'label' => 'Warehouse',
            'placeholder' => 'Select the warehouse',
            'helper_text' => '',
            'tooltip' => '',
        ],
        'container_type' => [
            'label' => 'Container',
            'placeholder' => 'Select the container',
            'helper_text' => '',
            'tooltip' => '',
        ],
        'net_weight_kg' => [
            'label' => 'Net weight (kg)',
            'placeholder' => '',
            'helper_text' => '',
            'tooltip' => '',
        ],
        'tare_weight_kg' => [
            'label' => 'Tare (kg)',
            'placeholder' => '',
            'helper_text' => '',
            'tooltip' => '',
        ],
        'location_id' => [
            'label' => 'Location',
            'placeholder' => 'Storage location (optional)',
            'helper_text' => '',
            'tooltip' => '',
        ],
        'notes' => [
            'label' => 'Notes',
            'placeholder' => '',
            'helper_text' => '',
            'tooltip' => '',
        ],
    ],
    'actions' => [
        'register_output' => [
            'label' => 'Register output',
            'tooltip' => 'Register a produced unit',
            'modal_heading' => 'Register output',
            'modal_description' => 'A new stock unit is created.',
            'success' => 'Output registered',
            'error' => 'Unable to register the output',
            'icon' => 'heroicon-o-plus-circle',
        ],
    ],
];
