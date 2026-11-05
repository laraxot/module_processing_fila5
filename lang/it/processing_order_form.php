<?php

declare(strict_types=1);

return [
    'fields' => [
        'machine_id' => [
            'label' => 'Macchina',
            'placeholder' => 'Seleziona la macchina',
            'helper_text' => '',
            'tooltip' => '',
        ],
        'operation_code' => [
            'label' => 'Codice operazione',
            'placeholder' => 'es. R12',
            'helper_text' => 'Codice dell\'operazione assegnato dal dominio (facoltativo)',
            'tooltip' => '',
        ],
        'planned_at' => [
            'label' => 'Pianificato per',
            'placeholder' => '',
            'helper_text' => '',
            'tooltip' => '',
        ],
        'notes' => [
            'label' => 'Note',
            'placeholder' => '',
            'helper_text' => '',
            'tooltip' => '',
        ],
    ],
];
