<?php

declare(strict_types=1);

return [
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
];
