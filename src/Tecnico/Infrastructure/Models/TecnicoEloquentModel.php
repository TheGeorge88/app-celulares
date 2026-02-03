<?php

declare(strict_types=1);

namespace Src\Tecnico\Infrastructure\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Src\OrdenReparacion\Infrastructure\Models\OrdenReparacionEloquentModel;

class TecnicoEloquentModel extends Model
{
    use HasUuid;

    protected $table = 'tecnicos';

    protected $fillable = [
        'id',
        'cedula',
        'nombre',
        'apellido',
        'telefono',
        'email',
        'especialidad',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function ordenesReparacion(): HasMany
    {
        return $this->hasMany(OrdenReparacionEloquentModel::class, 'tecnico_id', 'id');
    }
}
