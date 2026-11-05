<?php

declare(strict_types=1);

return [
<<<<<<< .merge_file_bj4k6r
<<<<<<< HEAD
=======
    'icon' => 'heroicon-o-cog-6-tooth',
    'navigation_sort' => 3,
>>>>>>> laraxot/dev
=======
    'icon' => 'heroicon-o-cog-6-tooth',
    'navigation_sort' => 3,
>>>>>>> .merge_file_zBksqx
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
