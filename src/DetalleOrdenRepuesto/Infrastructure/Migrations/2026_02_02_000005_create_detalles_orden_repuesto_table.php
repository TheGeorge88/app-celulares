<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detalles_orden_repuesto', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('orden_reparacion_id');
            $table->uuid('repuesto_id');
            $table->integer('cantidad');
            $table->decimal('precio_unitario', 10, 2);
            $table->decimal('subtotal', 10, 2);
            $table->timestamps();

            $table->foreign('orden_reparacion_id')
                ->references('id')
                ->on('ordenes_reparacion')
                ->onDelete('cascade');

            $table->foreign('repuesto_id')
                ->references('id')
                ->on('repuestos')
                ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detalles_orden_repuesto');
    }
};
