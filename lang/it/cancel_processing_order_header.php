<?php

declare(strict_types=1);

return [
    'fields' => [
        'reason' => [
            'label' => 'Motivo',
            'placeholder' => 'Perché l\'ordine viene annullato?',
            'helper_text' => '',
            'tooltip' => '',
        ],
    ],
    'actions' => [
        'cancel' => [
            'label' => 'Annulla',
            'tooltip' => 'Annulla l\'ordine',
            'modal_heading' => 'Annullare l\'ordine?',
            'modal_description' => 'Possibile solo se nessun materiale è stato movimentato.',
            'success' => 'Ordine annullato',
            'error' => 'Impossibile annullare l\'ordine',
            'icon' => 'heroicon-o-x-circle',
        ],
    ],
];
