<?php

declare(strict_types=1);

return [
    'label' => 'Outputs',
    'plural_label' => 'Outputs',
    'navigation' => [
        'label' => 'Outputs',
    ],
    'fields' => [
        'stockUnit' => [
            'code' => [
                'label' => 'Unit',
                'placeholder' => '',
                'helper_text' => '',
                'tooltip' => '',
            ],
        ],
        'kind' => [
            'label' => 'Output kind',
            'placeholder' => '',
            'helper_text' => '',
            'tooltip' => '',
        ],
        'quantity_kg' => [
            'label' => 'Quantity (kg)',
            'placeholder' => 'Empty = whole unit',
            'helper_text' => 'Leave empty to consume the whole unit',
            'tooltip' => '',
        ],
        'produced_at' => [
            'label' => 'Produced at',
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
