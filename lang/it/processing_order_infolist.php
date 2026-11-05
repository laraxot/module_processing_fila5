<?php

declare(strict_types=1);

return [
    'fields' => [
        'code' => [
            'label' => 'Codice',
            'placeholder' => '',
            'helper_text' => '',
            'tooltip' => '',
        ],
        'status' => [
            'label' => 'Stato',
            'placeholder' => '',
            'helper_text' => '',
            'tooltip' => '',
        ],
        'machine' => [
            'name' => [
                'label' => 'Macchina',
                'placeholder' => '',
                'helper_text' => '',
                'tooltip' => '',
            ],
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
        'started_at' => [
            'label' => 'Avviato il',
            'placeholder' => '',
            'helper_text' => '',
            'tooltip' => '',
        ],
        'completed_at' => [
            'label' => 'Completato il',
            'placeholder' => '',
            'helper_text' => '',
            'tooltip' => '',
        ],
        'cancelled_at' => [
            'label' => 'Annullato il',
            'placeholder' => '',
            'helper_text' => '',
            'tooltip' => '',
        ],
        'cancel_reason' => [
            'label' => 'Motivo annullamento',
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
        'input_kg' => [
            'label' => 'Input (kg)',
            'placeholder' => '',
            'helper_text' => '',
            'tooltip' => '',
        ],
        'output_kg' => [
            'label' => 'Output (kg)',
            'placeholder' => '',
            'helper_text' => '',
            'tooltip' => '',
        ],
        'loss_kg' => [
            'label' => 'Perdita (kg)',
            'placeholder' => '',
            'helper_text' => '',
            'tooltip' => '',
        ],
        'loss_pct' => [
            'label' => 'Perdita (%)',
            'placeholder' => '',
            'helper_text' => '',
            'tooltip' => '',
        ],
    ],
    'sections' => [
        'order' => [
            'label' => 'Ordine',
            'heading' => 'Ordine',
        ],
        'mass_balance' => [
            'label' => 'Bilancio di massa',
            'heading' => 'Bilancio di massa',
        ],
    ],
];
