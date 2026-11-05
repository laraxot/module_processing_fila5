<?php

declare(strict_types=1);

return [
    'navigation' => [
        'name' => 'Traceability',
        'plural' => 'Traceability',
        'group' => [
            'name' => 'Processing',
        ],
        'label' => 'Traceability',
        'icon' => 'heroicon-o-share',
        'sort' => 30,
    ],
    'title' => 'Unit traceability',
    'heading' => 'Unit traceability',
    'plural_label' => 'Traceability',
    'label' => 'Traceability',
    'fields' => [
        'code' => [
            'label' => 'Unit code',
            'placeholder' => 'Scan or type the code',
            'helper_text' => 'Barcode/RFID reader or code printed on the label',
            'tooltip' => '',
        ],
        'processing_order' => [
            'label' => 'Processing order',
            'placeholder' => '',
            'helper_text' => '',
            'tooltip' => 'Open the processing order',
        ],
    ],
    'actions' => [
        'search' => [
            'label' => 'Search',
            'tooltip' => 'Search the unit',
            'modal_heading' => 'Search',
            'modal_description' => '',
            'success' => '',
            'error' => '',
            'icon' => 'heroicon-o-magnifying-glass',
        ],
    ],
    'sections' => [
        'upstream' => [
            'heading' => 'Upstream',
            'description' => 'Which units it comes from, with the kg attributed to each',
        ],
        'downstream' => [
            'heading' => 'Downstream',
            'description' => 'Which units it ended up in, with the kg coming from it',
        ],
    ],
    'messages' => [
        'not_found' => 'No unit found with code :code',
        'unit_weight' => 'Current net weight',
        'empty_upstream' => 'No origin: the unit does not come from processing.',
        'empty_downstream' => 'No destination: the unit has not been processed.',
    ],
];
