<?php

declare(strict_types=1);

namespace Modules\Processing\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Modules\Processing\Database\Factories\LineageLinkFactory;
use Modules\Processing\Exceptions\ImmutableRecordException;
use Modules\Warehouse\Models\StockUnit;

/**
 * Arco della genealogia: l'unità figlia (output) deriva per la quota `share` dall'unità padre (input).
 *
 * `share = input_kg / totale_input_ordine`, `attributed_kg = output_kg * share`.
 * Scritto solo da CompleteProcessingOrderAction; immutabile.
 *
 * @property int $id
 * @property int $processing_order_id
 * @property int $parent_stock_unit_id
 * @property int $child_stock_unit_id
 * @property string $share
 * @property string $attributed_kg
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property-read ProcessingOrder $processingOrder
 * @property-read StockUnit|null $parentStockUnit
 * @property-read StockUnit|null $childStockUnit
 *
 * @method static LineageLinkFactory factory($count = null, $state = [])
 */
class LineageLink extends BaseModel
{
    /** @var list<string> */
    protected $fillable = [
        'processing_order_id',
        'parent_stock_unit_id',
        'child_stock_unit_id',
        'share',
        'attributed_kg',
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
    public function parentStockUnit(): BelongsTo
    {
        return $this->belongsTo(StockUnit::class, 'parent_stock_unit_id');
    }

    /**
     * @return BelongsTo<StockUnit, $this>
     */
    public function childStockUnit(): BelongsTo
    {
        return $this->belongsTo(StockUnit::class, 'child_stock_unit_id');
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
            'parent_stock_unit_id' => 'integer',
            'child_stock_unit_id' => 'integer',
            'share' => 'decimal:6',
            'attributed_kg' => 'decimal:3',
        ]);
    }
}
