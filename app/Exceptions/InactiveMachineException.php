<?php

declare(strict_types=1);

namespace Modules\Processing\Exceptions;

use Modules\Processing\Models\Machine;

/**
 * Non si pianificano lavorazioni su una macchina disattivata.
 */
final class InactiveMachineException extends ProcessingException
{
    public static function forMachine(Machine $machine): self
    {
        return new self(self::translated('inactive_machine', ['code' => $machine->code]));
    }
}
