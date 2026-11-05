# Modulo Processing

Lavorazioni generiche di un impianto: macchine, ordini di lavorazione che consumano unità di magazzino
(input) e ne producono di nuove (output), bilancio di massa alla chiusura, genealogia delle unità e
tracciabilità a monte e a valle.

Il modulo non sa nulla di rifiuti: il codice operazione (`operation_code`, es. `R12`) è una stringa
valorizzata dal dominio (modulo Roosko), che orchestra registri e FIR intorno a queste Actions.

## Dipendenze

- **Xot**: basi di modelli, migrazioni, Filament, provider, test.
- **Warehouse**: `StockUnit`, `StockUnitData`, `MovementTypeEnum`, `CreateStockUnitAction`, `ConsumeStockUnitAction`.
- **Labeling** (transitiva via Warehouse, facoltativa): `ResolveIdentifiableAction` per risolvere QR/RFID nella ricerca per codice.

I riferimenti a Warehouse (`stock_unit_id`, `parent_stock_unit_id`, `child_stock_unit_id`, `location_ref_id`)
sono colonne indicizzate **senza vincolo**: i moduli possono stare su connessioni o database diversi.

## Modelli (connessione `processing`)

| Modello | Tabella | Note |
|---|---|---|
| `Machine` | `machines` | soft delete; `type` = `MachineTypeEnum`; `location_ref_id` → Location di Warehouse |
| `ProcessingOrder` | `processing_orders` | `code` `PO-{anno}-{progr 6 cifre}`; `status` = `ProcessingOrderStatusEnum`; snapshot `input_kg`, `output_kg`, `loss_kg`, `loss_pct` alla chiusura |
| `ProcessingInput` | `processing_inputs` | unità consumata, kg, `consumed_at`; **immutabile** |
| `ProcessingOutput` | `processing_outputs` | unità prodotta, `kind` = `OutputKindEnum`, kg, `produced_at`; **immutabile**; `stock_unit_id` unico |
| `LineageLink` | `lineage_links` | arco padre → figlio con `share` (6 decimali) e `attributed_kg`; **immutabile**; unico per (ordine, padre, figlio) |

Pesi in kg `decimal(14,3)`, date `immutable_datetime`.

## Stati dell'ordine

```
draft ──start──▶ in_progress ──complete──▶ completed
  │                  │
  └──cancel──▶ cancelled ◀──cancel──┘   (solo senza materiale movimentato)
```

Le transizioni sono in `ProcessingOrderStatusEnum::canTransitionTo()`; le Actions fanno `Assert::true(...)`.
Solo `in_progress` accetta input e output (`acceptsMaterial()`).

## Actions

| Action | Firma | Cosa fa |
|---|---|---|
| `CreateProcessingOrderAction` | `execute(Machine $machine, ?string $operationCode = null, ?\DateTimeInterface $plannedAt = null, ?string $notes = null): ProcessingOrder` | bozza con codice progressivo per anno (lock + ritentativo su collisione unique) |
| `StartProcessingOrderAction` | `execute(ProcessingOrder $order): ProcessingOrder` | `draft → in_progress`, `started_at` |
| `AddProcessingInputAction` | `execute(ProcessingOrder $order, StockUnit $unit, ?float $kg = null): ProcessingInput` | kg null = intera unità; `ConsumeStockUnitAction` con reference all'ordine |
| `RegisterProcessingOutputAction` | `execute(ProcessingOrder $order, StockUnitData $unitData, OutputKindEnum $kind = OutputKindEnum::PRODUCT): ProcessingOutput` | `CreateStockUnitAction($unitData, MovementTypeEnum::PRODUCTION, $order)` |
| `CompleteProcessingOrderAction` | `execute(ProcessingOrder $order, bool $force = false): ProcessingOrder` | ≥1 input e ≥1 output; bilancio; soglia `processing.max_loss_pct`; LineageLink output × input; snapshot |
| `CancelProcessingOrderAction` | `execute(ProcessingOrder $order, string $reason): ProcessingOrder` | solo senza input consumati né output registrati; motivo obbligatorio |
| `CalculateMassBalanceAction` | `execute(array $inputsKg, array $outputsKg): MassBalanceData` | **puro** |
| `CalculateLineageSharesAction` | `execute(array $inputsKgByUnit, array $outputsKgByUnit): list<LineageShareData>` | **puro**: `share = input/totale`, `attributed = output × share` |
| `TraceUpstreamAction` | `execute(StockUnit $unit, int $maxDepth = 20): Collection<int, LineageNodeData>` | BFS sui `child → parent` |
| `TraceDownstreamAction` | `execute(StockUnit $unit, int $maxDepth = 20): Collection<int, LineageNodeData>` | BFS sui `parent → child` |
| `ResolveStockUnitByCodeAction` | `execute(string $scanned): ?StockUnit` | codice esatto, URL etichetta `.../l/{code}`, poi Labeling |
| `GenerateProcessingOrderCodeAction` | `execute(\DateTimeInterface $at): string` | interna, da chiamare in transazione |

Le Actions che toccano Warehouse aprono una transazione annidata sulle due connessioni
(`RunsInModuleTransactions`): atomica se condividono il database, best-effort altrimenti.

### Bilancio di massa

`lossKg = inputKg − outputKg`, `lossPct = lossKg / inputKg × 100`. Una perdita negativa (output più
pesante dell'input, es. umidità) è ammessa e non supera mai la soglia.

### Tracciabilità

Ogni `LineageNodeData` è un **cammino**, non un'unità: in un grafo a diamante la stessa unità può comparire
più volte con quote diverse (sommarle per `stockUnitId` dà la quota totale).

- `cumulativeShare`: prodotto delle `share` lungo il cammino.
- `attributedKg` a monte: kg dell'unità di partenza che provengono dal nodo.
- `attributedKg` a valle: kg del nodo che provengono dall'unità di partenza.
- `processingOrderId`: ordine dell'arco che porta al nodo; `viaStockUnitId` / `parentIndex`: nodo precedente.

Protezione dai cicli: un cammino non riattraversa i propri antenati; limiti `$maxDepth` e 5000 nodi.

## Filament

- `MachineResource`: lista, dettaglio, crea, modifica.
- `ProcessingOrderResource`: la creazione passa da `CreateProcessingOrderAction`; nella View le header actions
  *Avvia*, *Completa* (con "forza chiusura"), *Annulla* (con motivo); RelationManager **Input** con
  *Aggiungi input* (codice digitato o scansionato: lettori barcode/RFID in keyboard wedge, kg facoltativi) e
  **Output** con *Registra output* (nuova unità di magazzino). Input e output sono in sola lettura.
- `TraceabilityPage`: campo codice → albero a monte e a valle con kg, quota e link all'ordine;
  si apre anche con `?code=SU-2026-000123`.

I bottoni sono classi in `app/Filament/Actions` e chiamano solo le Actions di dominio. Tutti i testi stanno in
`lang/{it,en}`.

## Punti di estensione

- `config('processing.max_loss_pct')` (default `15.0`): soglia di perdita oltre cui la chiusura richiede `$force`.
- Le Actions sono queueable e risolte dal container: il modulo di prodotto le compone (es. Roosko
  `StartTreatmentAction` = Create + Start + AddInput + scritture di registro).

## Permessi

Policy in `app/Models/Policies`, permessi `{model_snake}.{viewAny,view,create,update,delete}`
(`machine.*`, `processing_order.*`, ...). Per input, output e genealogia `update`/`delete` sono sempre negati.

## Test

`tests/Unit`: bilancio di massa, quote di genealogia (1→1, 2→1, 2→2, perdite oltre soglia), transizioni di stato.
`tests/Feature`: tracciabilità su grafo a 3 livelli (anche con ciclo), ciclo completo di un ordine con Warehouse.
