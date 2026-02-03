<?php

declare(strict_types=1);

namespace Src\Repuesto\Infrastructure\Mappers;

use Src\Repuesto\Domain\Entities\Repuesto;
use Src\Repuesto\Infrastructure\Models\RepuestoEloquentModel;

class RepuestoMapper
{
    public static function toDomain(RepuestoEloquentModel $model): Repuesto
    {
        return new Repuesto(
            id: $model->id,
            codigo: $model->codigo,
            nombre: $model->nombre,
            descripcion: $model->descripcion ?? '',
            marca: $model->marca,
            modelo: $model->modelo,
            stock: $model->stock,
            stockMinimo: $model->stock_minimo,
            precioCompra: (float) $model->precio_compra,
            precioVenta: (float) $model->precio_venta,
            activo: $model->activo,
            createdAt: new \DateTimeImmutable($model->created_at->toDateTimeString()),
            updatedAt: new \DateTimeImmutable($model->updated_at->toDateTimeString())
        );
    }

    public static function toEloquent(Repuesto $repuesto): array
    {
        return [
            'id' => $repuesto->getId(),
            'codigo' => $repuesto->getCodigo(),
            'nombre' => $repuesto->getNombre(),
            'descripcion' => $repuesto->getDescripcion(),
            'marca' => $repuesto->getMarca(),
            'modelo' => $repuesto->getModelo(),
            'stock' => $repuesto->getStock(),
            'stock_minimo' => $repuesto->getStockMinimo(),
            'precio_compra' => $repuesto->getPrecioCompra(),
            'precio_venta' => $repuesto->getPrecioVenta(),
            'activo' => $repuesto->isActivo(),
        ];
    }
}
