<?php

declare(strict_types=1);

return [
    'icon' => 'heroicon-o-cog-6-tooth',
    'navigation_sort' => 3,
    'name' => 'Processing',
    'description' => 'Lavorazioni, bilancio di massa e tracciabilità',
    'navigation' => [
        'enabled' => true,
        'sort' => 40,
    ],

    /*
     * Perdita di massa massima ammessa alla chiusura di un ordine di lavorazione (in % sugli input).
     * Oltre questa soglia CompleteProcessingOrderAction rifiuta la chiusura salvo $force = true.
     * Dipende dal tipo di impianto: ogni installazione lo regola.
     */
    'max_loss_pct' => 15.0,
<<<<<<< HEAD
=======

    /*
     * Punto di estensione: azioni native sul materiale nel pannello Processing
     * ("Aggiungi input" e "Registra output" nei relation manager dell'ordine).
     * true (default): Processing usato da solo, le azioni consumano/creano unità di Warehouse.
     * false: un altro modulo gestisce il materiale con regole proprie (in Roosko: lotto, QR,
     * scarico di registro, autorizzazione) e lo spegne nel suo ServiceProvider:
     *   config(['processing.material_actions_enabled' => false]);
     * Le Actions di dominio (AddProcessingInputAction, RegisterProcessingOutputAction) restano
     * disponibili per il modulo che le usa: si nascondono solo i bottoni di Processing.
     */
    'material_actions_enabled' => true,
>>>>>>> laraxot/dev
];
