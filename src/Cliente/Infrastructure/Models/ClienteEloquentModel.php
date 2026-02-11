<?php

namespace Src\Cliente\Infrastructure\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Src\Auth\Infrastructure\Models\UserEloquentModel;
use Src\Factura\Infrastructure\Models\FacturaEloquentModel;

class ClienteEloquentModel extends Model
{
    use HasUuid;

    protected $table = 'clientes';

    protected $fillable = [
        'id',
        'user_id',
        'tipo_documento',
        'numero_documento',
        'razon_social',
        'direccion',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(UserEloquentModel::class, 'user_id', 'id');
    }

    public function facturas(): HasMany
    {
        return $this->hasMany(FacturaEloquentModel::class, 'cliente_id', 'id');
    }
}
