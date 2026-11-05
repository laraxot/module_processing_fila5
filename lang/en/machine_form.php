<?php

declare(strict_types=1);

return [
    'fields' => [
        'code' => [
            'label' => 'Code',
            'placeholder' => 'e.g. SHR-01',
            'helper_text' => 'Unique machine code',
            'tooltip' => '',
        ],
        'name' => [
            'label' => 'Name',
            'placeholder' => 'e.g. Primary shredder',
            'helper_text' => '',
            'tooltip' => '',
        ],
        'type' => [
            'label' => 'Type',
            'placeholder' => 'Select the type',
            'helper_text' => '',
            'tooltip' => '',
        ],
        'location_ref_id' => [
            'label' => 'Location',
            'placeholder' => 'Warehouse location',
            'helper_text' => 'Location where the machine is installed',
            'tooltip' => '',
        ],
        'capacity_kg_h' => [
            'label' => 'Capacity (kg/h)',
            'placeholder' => 'e.g. 12000',
            'helper_text' => 'Nominal hourly capacity',
            'tooltip' => '',
        ],
        'is_active' => [
            'label' => 'Active',
            'placeholder' => '',
            'helper_text' => 'Inactive machines do not accept new orders',
            'tooltip' => '',
        ],
    ],
];
