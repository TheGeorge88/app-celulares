<?php

declare(strict_types=1);

namespace Src\OrdenReparacion\Infrastructure\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Src\Cliente\Infrastructure\Models\ClienteEloquentModel;
use Src\Equipo\Infrastructure\Models\EquipoEloquentModel;
use Src\Tecnico\Infrastructure\Models\TecnicoEloquentModel;
use Src\DetalleOrdenRepuesto\Infrastructure\Models\DetalleOrdenRepuestoEloquentModel;

class OrdenReparacionEloquentModel extends Model
{
    use HasUuid;

    protected $table = 'ordenes_reparacion';

    protected $fillable = [
        'id',
        'codigo_seguimiento',
        'cliente_id',
        'equipo_id',
        'tecnico_id',
        'problema_reportado',
        'diagnostico',
        'solucion_aplicada',
        'estado',
        'costo_estimado',
        'costo_final',
        'autorizado',
        'fecha_autorizacion',
        'fecha_entrega',
        'observaciones',
    ];

    protected $casts = [
        'costo_estimado' => 'decimal:2',
        'costo_final' => 'decimal:2',
        'autorizado' => 'boolean',
        'fecha_autorizacion' => 'datetime',
        'fecha_entrega' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->codigo_seguimiento)) {
                $model->codigo_seguimiento = self::generarCodigoSeguimiento();
            }
        });
    }

    public static function generarCodigoSeguimiento(): string
    {
        $prefix = 'REP';
        $date = date('Ymd');
        $random = strtoupper(substr(md5(uniqid()), 0, 6));
        return "{$prefix}-{$date}-{$random}";
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(ClienteEloquentModel::class, 'cliente_id', 'id');
    }

    public function equipo(): BelongsTo
    {
        return $this->belongsTo(EquipoEloquentModel::class, 'equipo_id', 'id');
    }

    public function tecnico(): BelongsTo
    {
        return $this->belongsTo(TecnicoEloquentModel::class, 'tecnico_id', 'id');
    }

    public function detallesRepuestos(): HasMany
    {
        return $this->hasMany(DetalleOrdenRepuestoEloquentModel::class, 'orden_reparacion_id', 'id');
    }
}
