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
            $table->string('code', 50)->unique();
            $table->string('name');
            $table->string('type', 30)->index();
            // Location di Warehouse: cross-modulo, indice senza vincolo.
            $table->unsignedBigInteger('location_ref_id')->nullable()->index();
            $table->decimal('capacity_kg_h', 14, 3)->nullable();
            $table->boolean('is_active')->default(true)->index();
        });

        $this->tableUpdate(function (Blueprint $table): void {
            $this->updateTimestamps($table, true);
        });
    }
};
