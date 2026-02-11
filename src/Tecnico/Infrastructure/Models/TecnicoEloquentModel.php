<?php

declare(strict_types=1);

namespace Src\Tecnico\Infrastructure\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Src\Auth\Infrastructure\Models\UserEloquentModel;
use Src\OrdenReparacion\Infrastructure\Models\OrdenReparacionEloquentModel;

class TecnicoEloquentModel extends Model
{
    use HasUuid;

    protected $table = 'tecnicos';

    protected $fillable = [
        'id',
        'user_id',
        'especialidad',
        'certificacion',
        'fecha_contratacion',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'fecha_contratacion' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(UserEloquentModel::class, 'user_id');
    }

    public function ordenesReparacion(): HasMany
    {
        return $this->hasMany(OrdenReparacionEloquentModel::class, 'tecnico_id', 'id');
    }
}
