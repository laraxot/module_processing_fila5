<?php

declare(strict_types=1);

use Illuminate\Database\Schema\Blueprint;
use Modules\Xot\Database\Migrations\XotBaseMigration;

return new class extends XotBaseMigration
{
    public function up(): void
    {
        $this->tableCreate(static function (Blueprint $table): void {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('processing_order_id')->index();
            // StockUnit di Warehouse: cross-modulo, indici senza vincolo (BFS a monte su child, a valle su parent).
            $table->unsignedBigInteger('parent_stock_unit_id')->index();
            $table->unsignedBigInteger('child_stock_unit_id')->index();
            $table->decimal('share', 9, 6);
            $table->decimal('attributed_kg', 14, 3);
            $table->unique(['processing_order_id', 'parent_stock_unit_id', 'child_stock_unit_id'], 'lineage_links_order_parent_child_unique');
        });

        $this->tableUpdate(function (Blueprint $table): void {
            $this->updateTimestamps($table, false);
        });
    }

    /**
     * Mai `drop`: la tabella contiene genealogia delle unità (tracciabilità). Un `migrate:rollback` non deve
     * cancellare dati legali (audit database R15); si torna indietro solo con una migrazione in avanti.
     */
    public function down(): void
    {
        throw new \LogicException('Rollback vietato: '.$this->getTable().' contiene dati legali/di tracciabilità; usare una migrazione in avanti.');
    }
};
