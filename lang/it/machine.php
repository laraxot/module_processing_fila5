<?php

declare(strict_types=1);

return [
    'navigation' => [
        'name' => 'Macchina',
        'plural' => 'Macchine',
        'group' => [
            'name' => 'Lavorazioni',
        ],
        'label' => 'Macchine',
        'icon' => 'heroicon-o-cog-6-tooth',
        'sort' => 20,
    ],
    'label' => 'Macchina',
    'plural_label' => 'Macchine',
    'fields' => [
        'code' => [
            'label' => 'Codice',
            'placeholder' => 'es. TRI-01',
            'helper_text' => 'Codice univoco della macchina',
            'tooltip' => '',
        ],
        'name' => [
            'label' => 'Nome',
            'placeholder' => 'es. Trituratore primario',
            'helper_text' => '',
            'tooltip' => '',
        ],
        'type' => [
            'label' => 'Tipo',
            'placeholder' => 'Seleziona il tipo',
            'helper_text' => '',
            'tooltip' => '',
        ],
        'location_ref_id' => [
            'label' => 'Posizione',
            'placeholder' => 'Posizione di magazzino',
            'helper_text' => 'Posizione in cui è installata la macchina',
            'tooltip' => '',
        ],
        'location' => [
            'code' => [
                'label' => 'Posizione',
                'placeholder' => '',
                'helper_text' => '',
                'tooltip' => '',
            ],
        ],
        'capacity_kg_h' => [
            'label' => 'Capacità (kg/h)',
            'placeholder' => 'es. 12000',
            'helper_text' => 'Capacità nominale oraria',
            'tooltip' => '',
        ],
        'is_active' => [
            'label' => 'Attiva',
            'placeholder' => '',
            'helper_text' => 'Le macchine disattivate non accettano nuovi ordini',
            'tooltip' => '',
        ],
        'created_at' => [
            'label' => 'Creata il',
            'placeholder' => '',
            'helper_text' => '',
            'tooltip' => '',
        ],
        'updated_at' => [
            'label' => 'Modificata il',
            'placeholder' => '',
            'helper_text' => '',
            'tooltip' => '',
        ],
    ],
    'actions' => [
        'create' => [
            'label' => 'Nuova macchina',
            'tooltip' => '',
            'modal_heading' => 'Nuova macchina',
            'modal_description' => '',
            'success' => '',
            'error' => '',
        ],
        'view' => [
            'label' => 'Dettaglio',
            'tooltip' => '',
            'modal_heading' => 'Dettaglio',
            'modal_description' => '',
            'success' => '',
            'error' => '',
        ],
        'edit' => [
            'label' => 'Modifica',
            'tooltip' => '',
            'modal_heading' => 'Modifica',
            'modal_description' => '',
            'success' => '',
            'error' => '',
        ],
        'delete' => [
            'label' => 'Elimina',
            'tooltip' => '',
            'modal_heading' => 'Elimina',
            'modal_description' => '',
            'success' => '',
            'error' => '',
        ],
    ],
];
