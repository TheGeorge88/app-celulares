<?php

declare(strict_types=1);

namespace Src\Tecnico\Infrastructure\Mappers;

use Src\Tecnico\Domain\Entities\Tecnico;
use Src\Tecnico\Infrastructure\Models\TecnicoEloquentModel;

class TecnicoMapper
{
    public static function toDomain(TecnicoEloquentModel $model): Tecnico
    {
        return new Tecnico(
            id: $model->id,
            cedula: $model->cedula,
            nombre: $model->nombre,
            apellido: $model->apellido,
            telefono: $model->telefono,
            email: $model->email,
            especialidad: $model->especialidad,
            activo: $model->activo,
            createdAt: new \DateTimeImmutable($model->created_at->toDateTimeString()),
            updatedAt: new \DateTimeImmutable($model->updated_at->toDateTimeString())
        );
    }

    public static function toEloquent(Tecnico $tecnico): array
    {
        return [
            'id' => $tecnico->getId(),
            'cedula' => $tecnico->getCedula(),
            'nombre' => $tecnico->getNombre(),
            'apellido' => $tecnico->getApellido(),
            'telefono' => $tecnico->getTelefono(),
            'email' => $tecnico->getEmail(),
            'especialidad' => $tecnico->getEspecialidad(),
            'activo' => $tecnico->isActivo(),
        ];
    }
}
