<?php

declare(strict_types=1);

namespace Src\Equipo\Infrastructure\Mappers;

use Src\Equipo\Domain\Entities\Equipo;
use Src\Equipo\Infrastructure\Models\EquipoEloquentModel;

class EquipoMapper
{
    public static function toDomain(EquipoEloquentModel $model): Equipo
    {
        return new Equipo(
            id: $model->id,
            clienteId: $model->cliente_id,
            marca: $model->marca,
            modelo: $model->modelo,
            imei: $model->imei,
            color: $model->color,
            observaciones: $model->observaciones,
            createdAt: new \DateTimeImmutable($model->created_at->toDateTimeString()),
            updatedAt: new \DateTimeImmutable($model->updated_at->toDateTimeString())
        );
    }

    public static function toEloquent(Equipo $equipo): array
    {
        return [
            'id' => $equipo->getId(),
            'cliente_id' => $equipo->getClienteId(),
            'marca' => $equipo->getMarca(),
            'modelo' => $equipo->getModelo(),
            'imei' => $equipo->getImei(),
            'color' => $equipo->getColor(),
            'observaciones' => $equipo->getObservaciones(),
        ];
    }
}
