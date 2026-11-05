<?php

declare(strict_types=1);

return [
    'invalid_transition' => 'Order :code: transition from ":from" to ":to" is not allowed.',
    'not_accepting_material' => 'Order :code: in status ":status" inputs and outputs cannot be added (start it first).',
    'missing_inputs' => 'Order :code: at least one input is required to complete it.',
    'missing_outputs' => 'Order :code: at least one output is required to complete it.',
    'excessive_loss' => 'Order :code: mass loss :loss_pct% exceeds the :max_loss_pct% threshold. Check the weighings or force completion.',
    'non_positive_quantity' => 'Quantity must be greater than zero (given :kg kg).',
    'exceeds_available' => 'Unit :code: requested :requested kg, available :available kg.',
    'unit_is_own_output' => 'Unit :code is an output of order :order and cannot also be an input.',
    'unit_not_found' => 'No unit found with code :code.',
    'empty_inputs' => 'Total input is zero: lineage shares cannot be calculated.',
    'has_consumed_inputs' => 'Order :code: :count inputs already consumed, the order cannot be cancelled.',
    'has_registered_outputs' => 'Order :code: :count outputs already registered, the order cannot be cancelled (void the units in the warehouse first).',
    'missing_cancel_reason' => 'Order :code: a cancellation reason is required.',
    'inactive_machine' => 'Machine :code is inactive.',
    'immutable_record' => ':model #:id is a traceability record and cannot be changed or deleted.',
];
