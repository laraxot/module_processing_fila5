<?php

declare(strict_types=1);

return [
    'values' => [
        'draft' => [
            'label' => 'Draft',
            'color' => 'gray',
            'icon' => 'heroicon-o-pencil-square',
            'description' => 'Planned, not yet started',
        ],
        'in_progress' => [
            'label' => 'In progress',
            'color' => 'warning',
            'icon' => 'heroicon-o-play',
            'description' => 'Accepts inputs and outputs',
        ],
        'completed' => [
            'label' => 'Completed',
            'color' => 'success',
            'icon' => 'heroicon-o-check-circle',
            'description' => 'Mass balance and lineage recorded',
        ],
        'cancelled' => [
            'label' => 'Cancelled',
            'color' => 'danger',
            'icon' => 'heroicon-o-x-circle',
            'description' => 'Cancelled without moving material',
        ],
    ],
];
