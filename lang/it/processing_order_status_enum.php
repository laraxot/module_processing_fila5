<?php

declare(strict_types=1);

return [
    'values' => [
        'draft' => [
            'label' => 'Bozza',
            'color' => 'gray',
            'icon' => 'heroicon-o-pencil-square',
            'description' => 'Ordine pianificato, non ancora avviato',
        ],
        'in_progress' => [
            'label' => 'In lavorazione',
            'color' => 'warning',
            'icon' => 'heroicon-o-play',
            'description' => 'Accetta input e output',
        ],
        'completed' => [
            'label' => 'Completato',
            'color' => 'success',
            'icon' => 'heroicon-o-check-circle',
            'description' => 'Bilancio di massa e genealogia registrati',
        ],
        'cancelled' => [
            'label' => 'Annullato',
            'color' => 'danger',
            'icon' => 'heroicon-o-x-circle',
            'description' => 'Annullato senza movimentare materiale',
        ],
    ],
];
