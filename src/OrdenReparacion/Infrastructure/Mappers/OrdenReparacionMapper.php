<?php

declare(strict_types=1);

namespace Src\OrdenReparacion\Infrastructure\Mappers;

use Src\OrdenReparacion\Domain\Entities\OrdenReparacion;
use Src\OrdenReparacion\Infrastructure\Models\OrdenReparacionEloquentModel;

class OrdenReparacionMapper
{
    public static function toDomain(OrdenReparacionEloquentModel $model): OrdenReparacion
    {
        return new OrdenReparacion(
            id: $model->id,
            codigoSeguimiento: $model->codigo_seguimiento,
            clienteId: $model->cliente_id,
            equipoId: $model->equipo_id,
            tecnicoId: $model->tecnico_id,
            problemaReportado: $model->problema_reportado,
            diagnostico: $model->diagnostico,
            solucionAplicada: $model->solucion_aplicada,
            estado: $model->estado,
            costoEstimado: $model->costo_estimado ? (float) $model->costo_estimado : null,
            costoFinal: $model->costo_final ? (float) $model->costo_final : null,
            autorizado: $model->autorizado,
            fechaAutorizacion: $model->fecha_autorizacion
                ? new \DateTimeImmutable($model->fecha_autorizacion->toDateTimeString())
                : null,
            fechaEntrega: $model->fecha_entrega
                ? new \DateTimeImmutable($model->fecha_entrega->toDateTimeString())
                : null,
            observaciones: $model->observaciones,
            createdAt: new \DateTimeImmutable($model->created_at->toDateTimeString()),
            updatedAt: new \DateTimeImmutable($model->updated_at->toDateTimeString())
        );
    }

    public static function toEloquent(OrdenReparacion $orden): array
    {
        return [
            'id' => $orden->getId(),
            'codigo_seguimiento' => $orden->getCodigoSeguimiento(),
            'cliente_id' => $orden->getClienteId(),
            'equipo_id' => $orden->getEquipoId(),
            'tecnico_id' => $orden->getTecnicoId(),
            'problema_reportado' => $orden->getProblemaReportado(),
            'diagnostico' => $orden->getDiagnostico(),
            'solucion_aplicada' => $orden->getSolucionAplicada(),
            'estado' => $orden->getEstado(),
            'costo_estimado' => $orden->getCostoEstimado(),
            'costo_final' => $orden->getCostoFinal(),
            'autorizado' => $orden->isAutorizado(),
            'fecha_autorizacion' => $orden->getFechaAutorizacion()?->format('Y-m-d H:i:s'),
            'fecha_entrega' => $orden->getFechaEntrega()?->format('Y-m-d H:i:s'),
            'observaciones' => $orden->getObservaciones(),
        ];
    }
}
