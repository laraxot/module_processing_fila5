<?php

declare(strict_types=1);

namespace Modules\Processing\Filament\Pages;

use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Schema;
use Livewire\Attributes\Url;
use Modules\Processing\Actions\ResolveStockUnitByCodeAction;
use Modules\Processing\Actions\TraceDownstreamAction;
use Modules\Processing\Actions\TraceUpstreamAction;
use Modules\Processing\Datas\LineageNodeData;
use Modules\Processing\Filament\Resources\ProcessingOrderResource;
use Modules\Processing\Models\ProcessingOrder;
use Modules\Warehouse\Models\StockUnit;
use Modules\Xot\Actions\Cast\SafeIntCastAction;
use Modules\Xot\Actions\Cast\SafeStringCastAction;
use Modules\Xot\Filament\Pages\XotBasePage;
use Modules\Xot\Filament\Traits\NavigationLabelTrait;

/**
 * Tracciabilità di un'unità: campo codice (digitato o scansionato) → albero a monte e a valle
 * con kg attribuiti e ordine di lavorazione di ogni passaggio.
 *
 * Il codice è in query string (`?code=...`): la pagina si può aprire da un link o dal QR dell'etichetta.
 *
 * @property Schema $form
 */
class TraceabilityPage extends XotBasePage
{
    use NavigationLabelTrait;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-share';

    protected string $view = 'processing::filament.pages.traceability';

    #[Url]
    public string $code = '';

    public ?int $stockUnitId = null;

    public bool $notFound = false;

    public function mount(): void
    {
        $this->form->fill(['code' => $this->code]);
        if (trim($this->code) !== '') {
            $this->search();
        }
    }

    /**
     * @return array<string, Component>
     */
    public function getFormSchema(): array
    {
        return [
            'code' => TextInput::make('code')
                ->required()
                ->autofocus()
                ->autocomplete(false)
                ->maxLength(255),
        ];
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components($this->getFormSchema())
            ->statePath('data');
    }

    public function search(): void
    {
        $state = $this->form->getState();
        $this->code = trim(SafeStringCastAction::cast($state['code'] ?? $this->code));

        $unit = $this->code !== '' ? app(ResolveStockUnitByCodeAction::class)->execute($this->code) : null;
        $this->stockUnitId = $unit instanceof StockUnit ? SafeIntCastAction::cast($unit->getKey()) : null;
        $this->notFound = $this->code !== '' && $this->stockUnitId === null;
    }

    public function getStockUnit(): ?StockUnit
    {
        if ($this->stockUnitId === null) {
            return null;
        }

        return StockUnit::query()->find($this->stockUnitId);
    }

    /**
     * Alberi a monte e a valle e codici degli ordini citati, calcolati una sola volta per render.
     *
     * @return array{upstream: list<array{node: LineageNodeData, children: list<mixed>}>, downstream: list<array{node: LineageNodeData, children: list<mixed>}>, orderCodes: array<int, string>}
     */
    public function getTrace(): array
    {
        $unit = $this->getStockUnit();
        if ($unit === null) {
            return ['upstream' => [], 'downstream' => [], 'orderCodes' => []];
        }

        $upstream = app(TraceUpstreamAction::class)->execute($unit)->all();
        $downstream = app(TraceDownstreamAction::class)->execute($unit)->all();

        $orderIds = array_values(array_unique(array_map(
            static fn (LineageNodeData $node): int => $node->processingOrderId,
            [...$upstream, ...$downstream],
        )));

        /** @var array<int, string> $orderCodes */
        $orderCodes = ProcessingOrder::query()
            ->whereKey($orderIds)
            ->pluck('code', 'id')
            ->map(static fn (mixed $code): string => is_string($code) ? $code : '')
            ->all();

        return [
            'upstream' => $this->toTree($upstream),
            'downstream' => $this->toTree($downstream),
            'orderCodes' => $orderCodes,
        ];
    }

    public function getOrderUrl(int $orderId): string
    {
        return ProcessingOrderResource::getUrl('view', ['record' => $orderId]);
    }

    /**
     * Pubblica: la usa la vista.
     *
     * @return list<Action>
     */
    public function getFormActions(): array
    {
        return [
            Action::make('search')
                ->label(static::trans('actions.search.label'))
                ->icon('heroicon-o-magnifying-glass')
                ->submit('search'),
        ];
    }

    /**
     * Ricostruisce l'albero dai nodi piatti BFS usando `parentIndex`.
     *
     * @param  array<int, LineageNodeData>  $nodes
     * @return list<array{node: LineageNodeData, children: list<mixed>}>
     */
    private function toTree(array $nodes): array
    {
        /** @var array<int|string, list<int>> $childrenOf */
        $childrenOf = [];
        foreach ($nodes as $index => $node) {
            $childrenOf[$node->parentIndex ?? 'root'][] = $index;
        }

        return $this->branch('root', $childrenOf, $nodes);
    }

    /**
     * @param  array<int|string, list<int>>  $childrenOf
     * @param  array<int, LineageNodeData>  $nodes
     * @return list<array{node: LineageNodeData, children: list<mixed>}>
     */
    private function branch(int|string $key, array $childrenOf, array $nodes): array
    {
        $branch = [];
        foreach ($childrenOf[$key] ?? [] as $index) {
            $branch[] = ['node' => $nodes[$index], 'children' => $this->branch($index, $childrenOf, $nodes)];
        }

        return $branch;
    }
}
