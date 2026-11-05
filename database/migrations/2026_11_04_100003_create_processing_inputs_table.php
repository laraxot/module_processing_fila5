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
            // StockUnit di Warehouse: cross-modulo, indice senza vincolo.
            $table->unsignedBigInteger('stock_unit_id')->index();
            $table->decimal('quantity_kg', 14, 3);
            $table->timestamp('consumed_at');
        });

        $this->tableUpdate(function (Blueprint $table): void {
            $this->updateTimestamps($table, false);
        });
    }
};
