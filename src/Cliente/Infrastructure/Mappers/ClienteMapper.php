<?php

namespace Src\Cliente\Infrastructure\Mappers;

use Src\Cliente\Domain\Entities\Cliente;
use Src\Cliente\Infrastructure\Models\ClienteEloquentModel;

class ClienteMapper
{
    public static function toDomain(ClienteEloquentModel $model): Cliente
    {
        return new Cliente(
            id: $model->id,
            userId: $model->user_id,
            tipoDocumento: $model->tipo_documento,
            numeroDocumento: $model->numero_documento,
            razonSocial: $model->razon_social,
            direccion: $model->direccion,
            createdAt: new \DateTimeImmutable($model->created_at->toDateTimeString()),
            updatedAt: new \DateTimeImmutable($model->updated_at->toDateTimeString())
        );
    }

    public static function toEloquent(Cliente $cliente): array
    {
        return [
            'id' => $cliente->getId(),
            'user_id' => $cliente->getUserId(),
            'tipo_documento' => $cliente->getTipoDocumento(),
            'numero_documento' => $cliente->getNumeroDocumento(),
            'razon_social' => $cliente->getRazonSocial(),
            'direccion' => $cliente->getDireccion(),
        ];
    }
}
