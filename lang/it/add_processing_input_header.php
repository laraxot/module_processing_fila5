<?php

declare(strict_types=1);

return [
    'fields' => [
        'stock_unit_code' => [
            'label' => 'Codice unità',
            'placeholder' => 'Scansiona o digita il codice',
            'helper_text' => 'Lettore barcode/RFID: il codice viene inserito qui automaticamente',
            'tooltip' => '',
        ],
        'quantity_kg' => [
            'label' => 'Quantità (kg)',
            'placeholder' => 'Vuoto = intera unità',
            'helper_text' => 'Lascia vuoto per consumare l\'intera unità',
            'tooltip' => '',
        ],
    ],
    'actions' => [
        'add_input' => [
            'label' => 'Aggiungi input',
            'tooltip' => 'Carica un\'unità nella lavorazione',
            'modal_heading' => 'Aggiungi input',
            'modal_description' => 'Scansiona o digita il codice dell\'unità da caricare.',
            'success' => 'Input aggiunto',
            'error' => 'Impossibile aggiungere l\'input',
            'icon' => 'heroicon-o-qr-code',
        ],
    ],
];
