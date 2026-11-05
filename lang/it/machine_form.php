<?php

declare(strict_types=1);

return [
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
    ],
];
