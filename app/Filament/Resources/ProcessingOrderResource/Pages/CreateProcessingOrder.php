<?php

declare(strict_types=1);

namespace Modules\Processing\Filament\Resources\ProcessingOrderResource\Pages;

use Illuminate\Database\Eloquent\Model;
use Modules\Processing\Actions\CreateProcessingOrderAction;
use Modules\Processing\Filament\Resources\ProcessingOrderResource;
use Modules\Processing\Models\Machine;
use Modules\Xot\Actions\Cast\SafeIntCastAction;
use Modules\Xot\Actions\Cast\SafeNullableStringCastAction;
use Modules\Xot\Filament\Resources\Pages\XotBaseCreateRecord;
use Safe\DateTimeImmutable;

/**
 * La creazione passa da CreateProcessingOrderAction (codice PO-{anno}-{progr}, stato draft).
 */
class CreateProcessingOrder extends XotBaseCreateRecord
{
    protected static string $resource = ProcessingOrderResource::class;

    /**
     * @param  array<string, mixed>  $data
     */
    protected function handleRecordCreation(array $data): Model
    {
        $machine = Machine::query()->findOrFail(SafeIntCastAction::cast($data['machine_id'] ?? null));
        $plannedAt = $data['planned_at'] ?? null;

        return app(CreateProcessingOrderAction::class)->execute(
            $machine,
            SafeNullableStringCastAction::cast($data['operation_code'] ?? null),
            $plannedAt instanceof \DateTimeInterface ? $plannedAt : (is_string($plannedAt) && $plannedAt !== '' ? new DateTimeImmutable($plannedAt) : null),
            SafeNullableStringCastAction::cast($data['notes'] ?? null),
        );
    }

    protected function getRedirectUrl(): string
    {
        return ProcessingOrderResource::getUrl('view', ['record' => $this->getRecord()]);
    }
}
