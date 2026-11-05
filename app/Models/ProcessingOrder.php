<?php

declare(strict_types=1);

namespace Modules\Processing\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Modules\Processing\Database\Factories\ProcessingOrderFactory;
use Modules\Processing\Enums\ProcessingOrderStatusEnum;

/**
 * Ordine di lavorazione: consuma unità di magazzino (input) e ne produce di nuove (output).
 *
 * Lo stato si cambia solo con le Actions (Start/Complete/Cancel), che verificano
 * `ProcessingOrderStatusEnum::canTransitionTo()`. I campi `input_kg`, `output_kg`,
 * `loss_kg`, `loss_pct` sono lo snapshot del bilancio di massa alla chiusura.
 *
 * @property int $id
 * @property string $code
 * @property int $machine_id
 * @property string|null $operation_code
 * @property ProcessingOrderStatusEnum $status
 * @property CarbonImmutable|null $planned_at
 * @property CarbonImmutable|null $started_at
 * @property CarbonImmutable|null $completed_at
 * @property CarbonImmutable|null $cancelled_at
 * @property string|null $cancel_reason
 * @property string|null $input_kg
 * @property string|null $output_kg
 * @property string|null $loss_kg
 * @property string|null $loss_pct
 * @property string|null $notes
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property-read Machine $machine
 * @property-read \Illuminate\Database\Eloquent\Collection<int, ProcessingInput> $inputs
 * @property-read \Illuminate\Database\Eloquent\Collection<int, ProcessingOutput> $outputs
 * @property-read \Illuminate\Database\Eloquent\Collection<int, LineageLink> $lineageLinks
 *
 * @method static ProcessingOrderFactory factory($count = null, $state = [])
 */
class ProcessingOrder extends BaseModel
{
    /** @var list<string> */
    protected $fillable = [
        'code',
        'machine_id',
        'operation_code',
        'status',
        'planned_at',
        'started_at',
        'completed_at',
        'cancelled_at',
        'cancel_reason',
        'input_kg',
        'output_kg',
        'loss_kg',
        'loss_pct',
        'notes',
    ];

    /** @var array<string, mixed> */
    protected $attributes = [
        'status' => 'draft',
    ];

    /**
     * @return BelongsTo<Machine, $this>
     */
    public function machine(): BelongsTo
    {
        return $this->belongsTo(Machine::class)->withTrashed();
    }

    /**
     * @return HasMany<ProcessingInput, $this>
     */
    public function inputs(): HasMany
    {
        return $this->hasMany(ProcessingInput::class);
    }

    /**
     * @return HasMany<ProcessingOutput, $this>
     */
    public function outputs(): HasMany
    {
        return $this->hasMany(ProcessingOutput::class);
    }

    /**
     * @return HasMany<LineageLink, $this>
     */
    public function lineageLinks(): HasMany
    {
        return $this->hasMany(LineageLink::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return array_merge(parent::casts(), [
            'machine_id' => 'integer',
            'status' => ProcessingOrderStatusEnum::class,
            'planned_at' => 'immutable_datetime',
            'started_at' => 'immutable_datetime',
            'completed_at' => 'immutable_datetime',
            'cancelled_at' => 'immutable_datetime',
            'input_kg' => 'decimal:3',
            'output_kg' => 'decimal:3',
            'loss_kg' => 'decimal:3',
            'loss_pct' => 'decimal:3',
        ]);
    }
}
