<?php

declare(strict_types=1);

return [
    'label' => 'Inputs',
    'plural_label' => 'Inputs',
    'navigation' => [
        'label' => 'Inputs',
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
        'quantity_kg' => [
            'label' => 'Quantity (kg)',
            'placeholder' => 'Empty = whole unit',
            'helper_text' => 'Leave empty to consume the whole unit',
            'tooltip' => '',
        ],
        'consumed_at' => [
            'label' => 'Consumed at',
            'placeholder' => '',
            'helper_text' => '',
            'tooltip' => '',
        ],
        'stock_unit_code' => [
            'label' => 'Unit code',
            'placeholder' => 'Scan or type the code',
            'helper_text' => 'Barcode/RFID reader: the code is typed here automatically',
            'tooltip' => '',
        ],
    ],
    'actions' => [
        'add_input' => [
            'label' => 'Add input',
            'tooltip' => 'Load a unit into processing',
            'modal_heading' => 'Add input',
            'modal_description' => 'Scan or type the code of the unit to load.',
            'success' => 'Input added',
            'error' => 'Unable to add the input',
            'icon' => 'heroicon-o-qr-code',
        ],
    ],
];
