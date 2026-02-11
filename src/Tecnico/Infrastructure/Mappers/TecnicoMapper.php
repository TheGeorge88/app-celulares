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
            userId: $model->user_id,
            especialidad: $model->especialidad,
            certificacion: $model->certificacion,
            fechaContratacion: $model->fecha_contratacion
                ? new \DateTimeImmutable($model->fecha_contratacion->format('Y-m-d'))
                : null,
            activo: $model->activo,
            createdAt: new \DateTimeImmutable($model->created_at->toDateTimeString()),
            updatedAt: new \DateTimeImmutable($model->updated_at->toDateTimeString())
        );
    }

    public static function toEloquent(Tecnico $tecnico): array
    {
        return [
            'id' => $tecnico->getId(),
            'user_id' => $tecnico->getUserId(),
            'especialidad' => $tecnico->getEspecialidad(),
            'certificacion' => $tecnico->getCertificacion(),
            'fecha_contratacion' => $tecnico->getFechaContratacion()?->format('Y-m-d'),
            'activo' => $tecnico->isActivo(),
        ];
    }
}
