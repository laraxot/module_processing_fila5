<?php

declare(strict_types=1);

namespace Modules\Processing\Exceptions;

use Illuminate\Database\Eloquent\Model;

/**
 * Tentativo di modificare o eliminare un record di tracciabilità (input, output, arco di genealogia).
 */
final class ImmutableRecordException extends ProcessingException
{
    public static function forModel(Model $model): self
    {
        $key = $model->getKey();

        return new self(self::translated('immutable_record', [
            'model' => class_basename($model),
            'id' => is_scalar($key) ? (string) $key : '',
        ]));
    }
}
