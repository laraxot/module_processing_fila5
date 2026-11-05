<?php

declare(strict_types=1);

return [
    'navigation' => [
        'name' => 'Tracciabilità',
        'plural' => 'Tracciabilità',
        'group' => [
            'name' => 'Lavorazioni',
        ],
        'label' => 'Tracciabilità',
        'icon' => 'heroicon-o-share',
        'sort' => 30,
    ],
    'title' => 'Tracciabilità unità',
    'heading' => 'Tracciabilità unità',
    'plural_label' => 'Tracciabilità',
    'label' => 'Tracciabilità',
    'fields' => [
        'code' => [
            'label' => 'Codice unità',
            'placeholder' => 'Scansiona o digita il codice',
            'helper_text' => 'Lettore barcode/RFID o codice stampato sull\'etichetta',
            'tooltip' => '',
        ],
        'processing_order' => [
            'label' => 'Ordine di lavorazione',
            'placeholder' => '',
            'helper_text' => '',
            'tooltip' => 'Apri l\'ordine di lavorazione',
        ],
    ],
    'actions' => [
        'search' => [
            'label' => 'Cerca',
            'tooltip' => 'Cerca l\'unità',
            'modal_heading' => 'Cerca',
            'modal_description' => '',
            'success' => '',
            'error' => '',
            'icon' => 'heroicon-o-magnifying-glass',
        ],
    ],
    'sections' => [
        'upstream' => [
            'heading' => 'A monte',
            'description' => 'Da quali unità deriva, con i kg attribuiti a ciascuna',
        ],
        'downstream' => [
            'heading' => 'A valle',
            'description' => 'In quali unità è finita, con i kg che ne provengono',
        ],
    ],
    'messages' => [
        'not_found' => 'Nessuna unità trovata con codice :code',
        'unit_weight' => 'Peso netto attuale',
        'empty_upstream' => 'Nessuna origine: l\'unità non deriva da lavorazioni.',
        'empty_downstream' => 'Nessuna destinazione: l\'unità non è stata lavorata.',
    ],
];
