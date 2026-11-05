<?php

declare(strict_types=1);

return [
    'invalid_transition' => 'Ordine :code: passaggio da ":from" a ":to" non consentito.',
    'not_accepting_material' => 'Ordine :code: nello stato ":status" non si aggiungono input o output (va prima avviato).',
    'missing_inputs' => 'Ordine :code: serve almeno un input per completarlo.',
    'missing_outputs' => 'Ordine :code: serve almeno un output per completarlo.',
    'excessive_loss' => 'Ordine :code: perdita di massa :loss_pct% oltre la soglia del :max_loss_pct%. Verifica le pesate o forza la chiusura.',
    'non_positive_quantity' => 'La quantità deve essere maggiore di zero (indicati :kg kg).',
    'exceeds_available' => 'Unità :code: richiesti :requested kg, disponibili :available kg.',
    'unit_is_own_output' => 'L\'unità :code è un output dell\'ordine :order e non può esserne anche input.',
    'unit_not_found' => 'Nessuna unità trovata con codice :code.',
    'empty_inputs' => 'Il totale degli input è zero: impossibile calcolare le quote di genealogia.',
    'has_consumed_inputs' => 'Ordine :code: :count input già consumati, l\'ordine non si può annullare.',
    'has_registered_outputs' => 'Ordine :code: :count output già registrati, l\'ordine non si può annullare (stornare prima le unità in magazzino).',
    'missing_cancel_reason' => 'Ordine :code: il motivo dell\'annullamento è obbligatorio.',
    'inactive_machine' => 'La macchina :code è disattivata.',
    'immutable_record' => ':model #:id è un record di tracciabilità e non può essere modificato o eliminato.',
];
