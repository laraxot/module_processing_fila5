<?php

declare(strict_types=1);

return [
    'label' => 'Output',
    'plural_label' => 'Output',
    'navigation' => [
        'label' => 'Output',
    ],
    'fields' => [
        'stockUnit' => [
            'code' => [
                'label' => 'Unità',
                'placeholder' => '',
                'helper_text' => '',
                'tooltip' => '',
            ],
        ],
        'kind' => [
            'label' => 'Tipo output',
            'placeholder' => '',
            'helper_text' => '',
            'tooltip' => '',
        ],
        'quantity_kg' => [
            'label' => 'Quantità (kg)',
            'placeholder' => 'Vuoto = intera unità',
            'helper_text' => 'Lascia vuoto per consumare l\'intera unità',
            'tooltip' => '',
        ],
        'produced_at' => [
            'label' => 'Prodotto il',
            'placeholder' => '',
            'helper_text' => '',
            'tooltip' => '',
        ],
        'warehouse_id' => [
            'label' => 'Magazzino',
            'placeholder' => 'Seleziona il magazzino',
            'helper_text' => '',
            'tooltip' => '',
        ],
        'container_type' => [
            'label' => 'Contenitore',
            'placeholder' => 'Seleziona il contenitore',
            'helper_text' => '',
            'tooltip' => '',
        ],
        'net_weight_kg' => [
            'label' => 'Peso netto (kg)',
            'placeholder' => '',
            'helper_text' => '',
            'tooltip' => '',
        ],
        'tare_weight_kg' => [
            'label' => 'Tara (kg)',
            'placeholder' => '',
            'helper_text' => '',
            'tooltip' => '',
        ],
        'location_id' => [
            'label' => 'Posizione',
            'placeholder' => 'Posizione di stoccaggio (facoltativa)',
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
    'actions' => [
        'register_output' => [
            'label' => 'Registra output',
            'tooltip' => 'Registra un\'unità prodotta',
            'modal_heading' => 'Registra output',
            'modal_description' => 'Viene creata una nuova unità di magazzino.',
            'success' => 'Output registrato',
            'error' => 'Impossibile registrare l\'output',
            'icon' => 'heroicon-o-plus-circle',
        ],
    ],
];
