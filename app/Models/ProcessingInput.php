<?php

declare(strict_types=1);

namespace Modules\Processing\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Modules\Processing\Database\Factories\ProcessingInputFactory;
use Modules\Processing\Exceptions\ImmutableRecordException;
use Modules\Warehouse\Models\StockUnit;

/**
 * Consumo di un'unità di magazzino da parte di un ordine di lavorazione.
 *
 * Immutabile: il movimento di consumo è già registrato in Warehouse e non si annulla da qui.
 *
 * @property int $id
 * @property int $processing_order_id
 * @property int $stock_unit_id StockUnit di Warehouse (cross-modulo, senza vincolo)
 * @property string $quantity_kg
 * @property CarbonImmutable $consumed_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property-read ProcessingOrder $processingOrder
 * @property-read StockUnit|null $stockUnit
 *
 * @method static ProcessingInputFactory factory($count = null, $state = [])
 */
class ProcessingInput extends BaseModel
{
    /** @var list<string> */
    protected $fillable = [
        'processing_order_id',
        'stock_unit_id',
        'quantity_kg',
        'consumed_at',
    ];

    /**
     * @return BelongsTo<ProcessingOrder, $this>
     */
    public function processingOrder(): BelongsTo
    {
        return $this->belongsTo(ProcessingOrder::class);
    }

    /**
     * @return BelongsTo<StockUnit, $this>
     */
    public function stockUnit(): BelongsTo
    {
        return $this->belongsTo(StockUnit::class);
    }

    protected static function booted(): void
    {
        static::updating(static function (self $model): never {
            throw ImmutableRecordException::forModel($model);
        });
        static::deleting(static function (self $model): never {
            throw ImmutableRecordException::forModel($model);
        });
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return array_merge(parent::casts(), [
            'processing_order_id' => 'integer',
            'stock_unit_id' => 'integer',
            'quantity_kg' => 'decimal:3',
            'consumed_at' => 'immutable_datetime',
        ]);
    }
}
