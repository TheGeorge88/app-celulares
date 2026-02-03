<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Agregar columna orden_reparacion_id a facturas
        Schema::table('facturas', function (Blueprint $table) {
            $table->uuid('orden_reparacion_id')->nullable()->after('cliente_id');
            $table->decimal('costo_mano_obra', 10, 2)->default(0)->after('subtotal');
            $table->decimal('costo_repuestos', 10, 2)->default(0)->after('costo_mano_obra');
            $table->string('metodo_pago', 50)->nullable()->after('estado');

            $table->foreign('orden_reparacion_id')
                ->references('id')
                ->on('ordenes_reparacion')
                ->onDelete('restrict');
        });

        // Modificar detalle_facturas para permitir conceptos de servicio
        Schema::table('detalle_facturas', function (Blueprint $table) {
            // Hacer producto_id nullable para permitir conceptos de servicio
            $table->uuid('producto_id')->nullable()->change();
            $table->uuid('repuesto_id')->nullable()->after('producto_id');
            $table->enum('tipo', ['producto', 'repuesto', 'mano_obra', 'servicio'])->default('producto')->after('id');
            $table->string('concepto', 255)->nullable()->after('tipo');

            $table->foreign('repuesto_id')
                ->references('id')
                ->on('repuestos')
                ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::table('detalle_facturas', function (Blueprint $table) {
            $table->dropForeign(['repuesto_id']);
            $table->dropColumn(['repuesto_id', 'tipo', 'concepto']);
        });

        Schema::table('facturas', function (Blueprint $table) {
            $table->dropForeign(['orden_reparacion_id']);
            $table->dropColumn(['orden_reparacion_id', 'costo_mano_obra', 'costo_repuestos', 'metodo_pago']);
        });
    }
};
