<?php

declare(strict_types=1);

return [
    'fields' => [
        'reason' => [
            'label' => 'Reason',
            'placeholder' => 'Why is the order being cancelled?',
            'helper_text' => '',
            'tooltip' => '',
        ],
    ],
    'actions' => [
        'cancel' => [
            'label' => 'Cancel',
            'tooltip' => 'Cancel the order',
            'modal_heading' => 'Cancel the order?',
            'modal_description' => 'Only possible if no material has been moved.',
            'success' => 'Order cancelled',
            'error' => 'Unable to cancel the order',
            'icon' => 'heroicon-o-x-circle',
        ],
    ],
];
