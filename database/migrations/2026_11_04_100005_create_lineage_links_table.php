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
};
