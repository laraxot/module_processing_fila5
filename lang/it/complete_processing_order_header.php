<?php

declare(strict_types=1);

return [
    'fields' => [
        'force' => [
            'label' => 'Forza la chiusura',
            'placeholder' => '',
            'helper_text' => 'Chiudi anche se la perdita di massa supera la soglia configurata',
            'tooltip' => '',
        ],
    ],
    'actions' => [
        'complete' => [
            'label' => 'Completa',
            'tooltip' => 'Chiudi l\'ordine con bilancio di massa',
            'modal_heading' => 'Completare l\'ordine?',
            'modal_description' => 'Viene calcolato il bilancio di massa e scritta la genealogia delle unità prodotte.',
            'success' => 'Ordine completato',
            'error' => 'Impossibile completare l\'ordine',
            'icon' => 'heroicon-o-check-circle',
        ],
    ],
];
