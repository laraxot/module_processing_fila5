<?php

declare(strict_types=1);

namespace Modules\Processing\Models;

use Modules\Xot\Models\XotBaseModel;

/**
 * Base Model del modulo Processing.
 *
 * Connessione dedicata `processing`: i riferimenti ai modelli di Warehouse
 * (stock_unit_id, location_ref_id) sono colonne indicizzate senza vincolo.
 */
abstract class BaseModel extends XotBaseModel
{
    /** @var string */
    protected $connection = 'processing';

    /**
     * Chiavi primarie bigIncrements: l'id resta intero (XotBaseModel lo casta a stringa).
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return array_merge(parent::casts(), [
            'id' => 'integer',
        ]);
    }
}
