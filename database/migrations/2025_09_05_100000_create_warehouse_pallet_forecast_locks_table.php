<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('warehouse_pallet_forecast_locks', function (Blueprint $table) {
            $table->bigIncrements('i_id');
            $table->unsignedBigInteger('i_warehouse_id');
            $table->date('dt_forecast_date');
            $table->unsignedInteger('i_pallet_forecast')->default(0);
            $table->timestamp('dt_locked_at')->nullable();
            $table->tinyInteger('t_is_active')->default(1);
            $table->tinyInteger('t_is_deleted')->default(0);
            $table->unsignedBigInteger('i_created_id')->nullable();
            $table->unsignedBigInteger('i_updated_id')->nullable();
            $table->unsignedBigInteger('i_deleted_id')->nullable();
            $table->timestamp('dt_created_at')->useCurrent();
            $table->timestamp('dt_updated_at')->nullable();
            $table->timestamp('dt_deleted_at')->nullable();

            $table->unique(['i_warehouse_id','dt_forecast_date'], 'uniq_warehouse_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('warehouse_pallet_forecast_locks');
    }
};
