<?php

declare(strict_types=1);

namespace Modules\Processing\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Modules\Processing\Database\Factories\MachineFactory;
use Modules\Processing\Enums\MachineTypeEnum;
use Modules\Warehouse\Models\Location;

/**
 * Macchina di lavorazione (trituratore, vaglio, separatore...).
 *
 * @property int $id
 * @property string $code
 * @property string $name
 * @property MachineTypeEnum $type
 * @property int|null $location_ref_id Location di Warehouse dove è installata (cross-modulo, senza vincolo)
 * @property string|null $capacity_kg_h
 * @property bool $is_active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @property-read Location|null $location
 * @property-read \Illuminate\Database\Eloquent\Collection<int, ProcessingOrder> $processingOrders
 *
 * @method static MachineFactory factory($count = null, $state = [])
 */
class Machine extends BaseModel
{
    use SoftDeletes;

    /** @var list<string> */
    protected $fillable = [
        'code',
        'name',
        'type',
        'location_ref_id',
        'capacity_kg_h',
        'is_active',
    ];

    /**
     * @return BelongsTo<Location, $this>
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_ref_id');
    }

    /**
     * @return HasMany<ProcessingOrder, $this>
     */
    public function processingOrders(): HasMany
    {
        return $this->hasMany(ProcessingOrder::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return array_merge(parent::casts(), [
            'type' => MachineTypeEnum::class,
            'location_ref_id' => 'integer',
            'capacity_kg_h' => 'decimal:3',
            'is_active' => 'boolean',
        ]);
    }
}
