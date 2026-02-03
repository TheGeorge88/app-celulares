<?php

namespace Src\Factura\Infrastructure\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Src\Producto\Infrastructure\Models\ProductoEloquentModel;
use Src\Repuesto\Infrastructure\Models\RepuestoEloquentModel;

class DetalleFacturaEloquentModel extends Model
{
    use HasUuid;

    protected $table = 'detalle_facturas';

    protected $fillable = [
        'id',
        'factura_id',
        'tipo',
        'concepto',
        'producto_id',
        'repuesto_id',
        'cantidad',
        'precio_unitario',
        'descuento',
        'subtotal'
    ];

    protected $casts = [
        'cantidad' => 'integer',
        'precio_unitario' => 'decimal:2',
        'descuento' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->subtotal = ($model->cantidad * $model->precio_unitario) - ($model->descuento ?? 0);
        });

        static::updating(function ($model) {
            $model->subtotal = ($model->cantidad * $model->precio_unitario) - ($model->descuento ?? 0);
        });
    }

    public function factura(): BelongsTo
    {
        return $this->belongsTo(FacturaEloquentModel::class, 'factura_id', 'id');
    }

    public function producto(): BelongsTo
    {
        return $this->belongsTo(ProductoEloquentModel::class, 'producto_id', 'id');
    }

    public function repuesto(): BelongsTo
    {
        return $this->belongsTo(RepuestoEloquentModel::class, 'repuesto_id', 'id');
    }
}
