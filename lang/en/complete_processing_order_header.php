<?php

declare(strict_types=1);

return [
    'fields' => [
        'force' => [
            'label' => 'Force completion',
            'placeholder' => '',
            'helper_text' => 'Complete even if mass loss exceeds the configured threshold',
            'tooltip' => '',
        ],
    ],
    'actions' => [
        'complete' => [
            'label' => 'Complete',
            'tooltip' => 'Close the order with mass balance',
            'modal_heading' => 'Complete the order?',
            'modal_description' => 'The mass balance is calculated and the lineage of produced units is written.',
            'success' => 'Order completed',
            'error' => 'Unable to complete the order',
            'icon' => 'heroicon-o-check-circle',
        ],
    ],
];
