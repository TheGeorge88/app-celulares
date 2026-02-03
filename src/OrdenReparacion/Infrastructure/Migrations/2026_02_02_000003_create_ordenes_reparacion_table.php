<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ordenes_reparacion', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('codigo_seguimiento', 30)->unique();
            $table->uuid('cliente_id');
            $table->uuid('equipo_id');
            $table->uuid('tecnico_id')->nullable();
            $table->text('problema_reportado');
            $table->text('diagnostico')->nullable();
            $table->text('solucion_aplicada')->nullable();
            $table->enum('estado', [
                'RECIBIDO',
                'EN_DIAGNOSTICO',
                'PENDIENTE_AUTORIZACION',
                'AUTORIZADO',
                'EN_REPARACION',
                'REPARADO',
                'ENTREGADO',
                'CANCELADO'
            ])->default('RECIBIDO');
            $table->decimal('costo_estimado', 10, 2)->nullable();
            $table->decimal('costo_final', 10, 2)->nullable();
            $table->boolean('autorizado')->default(false);
            $table->timestamp('fecha_autorizacion')->nullable();
            $table->timestamp('fecha_entrega')->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();

            $table->foreign('cliente_id')
                ->references('id')
                ->on('clientes')
                ->onDelete('restrict');

            $table->foreign('equipo_id')
                ->references('id')
                ->on('equipos')
                ->onDelete('restrict');

            $table->foreign('tecnico_id')
                ->references('id')
                ->on('tecnicos')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ordenes_reparacion');
    }
};
