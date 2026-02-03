<?php

declare(strict_types=1);

namespace Src\Equipo\Infrastructure\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Src\Cliente\Infrastructure\Models\ClienteEloquentModel;
use Src\OrdenReparacion\Infrastructure\Models\OrdenReparacionEloquentModel;

class EquipoEloquentModel extends Model
{
    use HasUuid;

    protected $table = 'equipos';

    protected $fillable = [
        'id',
        'cliente_id',
        'marca',
        'modelo',
        'imei',
        'color',
        'observaciones',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(ClienteEloquentModel::class, 'cliente_id', 'id');
    }

    public function ordenesReparacion(): HasMany
    {
        return $this->hasMany(OrdenReparacionEloquentModel::class, 'equipo_id', 'id');
    }
}
