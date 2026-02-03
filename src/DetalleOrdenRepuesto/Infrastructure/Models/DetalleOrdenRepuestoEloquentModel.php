<?php

declare(strict_types=1);

namespace Src\DetalleOrdenRepuesto\Infrastructure\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Src\OrdenReparacion\Infrastructure\Models\OrdenReparacionEloquentModel;
use Src\Repuesto\Infrastructure\Models\RepuestoEloquentModel;

class DetalleOrdenRepuestoEloquentModel extends Model
{
    use HasUuid;

    protected $table = 'detalles_orden_repuesto';

    protected $fillable = [
        'id',
        'orden_reparacion_id',
        'repuesto_id',
        'cantidad',
        'precio_unitario',
        'subtotal',
    ];

    protected $casts = [
        'cantidad' => 'integer',
        'precio_unitario' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->subtotal = $model->cantidad * $model->precio_unitario;
        });

        static::updating(function ($model) {
            $model->subtotal = $model->cantidad * $model->precio_unitario;
        });
    }

    public function ordenReparacion(): BelongsTo
    {
        return $this->belongsTo(OrdenReparacionEloquentModel::class, 'orden_reparacion_id', 'id');
    }

    public function repuesto(): BelongsTo
    {
        return $this->belongsTo(RepuestoEloquentModel::class, 'repuesto_id', 'id');
    }
}
