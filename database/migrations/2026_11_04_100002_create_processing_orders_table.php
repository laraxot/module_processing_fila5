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
            $table->string('code', 30)->unique();
            $table->unsignedBigInteger('machine_id')->index();
            $table->string('operation_code', 20)->nullable();
            $table->string('status', 20)->default('draft')->index();
            $table->timestamp('planned_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancel_reason')->nullable();
            $table->decimal('input_kg', 14, 3)->nullable();
            $table->decimal('output_kg', 14, 3)->nullable();
            $table->decimal('loss_kg', 14, 3)->nullable();
            $table->decimal('loss_pct', 9, 3)->nullable();
            $table->text('notes')->nullable();
        });

        $this->tableUpdate(function (Blueprint $table): void {
            $this->updateTimestamps($table, false);
        });
    }
};
