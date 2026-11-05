<?php

declare(strict_types=1);

return [
    'fields' => [
        'stock_unit_code' => [
            'label' => 'Unit code',
            'placeholder' => 'Scan or type the code',
            'helper_text' => 'Barcode/RFID reader: the code is typed here automatically',
            'tooltip' => '',
        ],
        'quantity_kg' => [
            'label' => 'Quantity (kg)',
            'placeholder' => 'Empty = whole unit',
            'helper_text' => 'Leave empty to consume the whole unit',
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
