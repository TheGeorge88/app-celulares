<?php

declare(strict_types=1);

namespace Src\Repuesto\Infrastructure\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Src\DetalleOrdenRepuesto\Infrastructure\Models\DetalleOrdenRepuestoEloquentModel;

class RepuestoEloquentModel extends Model
{
    use HasUuid;

    protected $table = 'repuestos';

    protected $fillable = [
        'id',
        'codigo',
        'nombre',
        'descripcion',
        'marca',
        'modelo',
        'stock',
        'stock_minimo',
        'precio_compra',
        'precio_venta',
        'activo',
    ];

    protected $casts = [
        'stock' => 'integer',
        'stock_minimo' => 'integer',
        'precio_compra' => 'decimal:2',
        'precio_venta' => 'decimal:2',
        'activo' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function detallesOrden(): HasMany
    {
        return $this->hasMany(DetalleOrdenRepuestoEloquentModel::class, 'repuesto_id', 'id');
    }

    public function tieneStockBajo(): bool
    {
        return $this->stock <= $this->stock_minimo;
    }

    public function hayDisponibilidad(int $cantidad): bool
    {
        return $this->stock >= $cantidad;
    }

    public function descontarStock(int $cantidad): void
    {
        $this->stock -= $cantidad;
        $this->save();
    }

    public function agregarStock(int $cantidad): void
    {
        $this->stock += $cantidad;
        $this->save();
    }
}
