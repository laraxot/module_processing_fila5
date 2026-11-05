<?php

declare(strict_types=1);

namespace Modules\Processing\Exceptions;

use DomainException;

/**
 * Radice delle eccezioni di dominio del modulo Processing.
 *
 * Il messaggio è già tradotto (lang/{locale}/processing_exception.php) e può essere mostrato all'operatore.
 */
abstract class ProcessingException extends DomainException
{
    /**
     * @param  array<string, string|int|float>  $replace
     */
    protected static function translated(string $key, array $replace = []): string
    {
        $message = trans('processing::processing_exception.'.$key, $replace);

        return is_string($message) ? $message : $key;
    }
}
