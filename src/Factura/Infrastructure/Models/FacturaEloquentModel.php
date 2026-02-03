<?php

namespace Src\Factura\Infrastructure\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Src\Cliente\Infrastructure\Models\ClienteEloquentModel;
use Src\Factura\Infrastructure\Models\DetalleFacturaEloquentModel;
use Src\Auth\Infrastructure\Models\UserEloquentModel;
use Src\OrdenReparacion\Infrastructure\Models\OrdenReparacionEloquentModel;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FacturaEloquentModel extends Model
{
    use HasUuid;

    protected $table = 'facturas';

    protected $fillable = [
        'id',
        'numero_factura',
        'serie',
        'cliente_id',
        'orden_reparacion_id',
        'usuario_id',
        'fecha_emision',
        'fecha_vencimiento',
        'subtotal',
        'costo_mano_obra',
        'costo_repuestos',
        'igv',
        'descuento',
        'total',
        'estado',
        'metodo_pago',
        'observaciones'
    ];

    protected $casts = [
        'fecha_emision' => 'date',
        'fecha_vencimiento' => 'date',
        'subtotal' => 'decimal:2',
        'costo_mano_obra' => 'decimal:2',
        'costo_repuestos' => 'decimal:2',
        'igv' => 'decimal:2',
        'descuento' => 'decimal:2',
        'total' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->numero_factura)) {
                $model->numero_factura = self::generarNumeroFactura($model->serie);
            }
        });
    }

    public static function generarNumeroFactura(string $serie = 'F001'): string
    {
        $ultimaFactura = self::where('serie', $serie)
            ->orderBy('created_at', 'desc')
            ->first();

        if ($ultimaFactura) {
            $partes = explode('-', $ultimaFactura->numero_factura);
            $numero = (int) end($partes) + 1;
        } else {
            $numero = 1;
        }

        return $serie . '-' . str_pad((string) $numero, 8, '0', STR_PAD_LEFT);
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(ClienteEloquentModel::class, 'cliente_id', 'id');
    }

    public function ordenReparacion(): BelongsTo
    {
        return $this->belongsTo(OrdenReparacionEloquentModel::class, 'orden_reparacion_id', 'id');
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(DetalleFacturaEloquentModel::class, 'factura_id', 'id');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(UserEloquentModel::class, 'usuario_id', 'id');
    }

    public function calcularTotales(): void
    {
        $subtotalDetalles = $this->detalles()->sum('subtotal');
        $this->subtotal = $subtotalDetalles;
        $this->igv = $subtotalDetalles * 0.12; // 12% IVA Ecuador
        $this->total = $this->subtotal + $this->igv - $this->descuento;
        $this->save();
    }
}
