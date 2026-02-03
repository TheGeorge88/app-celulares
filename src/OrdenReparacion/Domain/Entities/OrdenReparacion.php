<?php

declare(strict_types=1);

namespace Src\OrdenReparacion\Domain\Entities;

use DateTimeImmutable;

class OrdenReparacion
{
    public const ESTADO_RECIBIDO = 'RECIBIDO';
    public const ESTADO_EN_DIAGNOSTICO = 'EN_DIAGNOSTICO';
    public const ESTADO_PENDIENTE_AUTORIZACION = 'PENDIENTE_AUTORIZACION';
    public const ESTADO_AUTORIZADO = 'AUTORIZADO';
    public const ESTADO_EN_REPARACION = 'EN_REPARACION';
    public const ESTADO_REPARADO = 'REPARADO';
    public const ESTADO_ENTREGADO = 'ENTREGADO';
    public const ESTADO_CANCELADO = 'CANCELADO';

    private string $id;
    private string $codigoSeguimiento;
    private string $clienteId;
    private string $equipoId;
    private ?string $tecnicoId;
    private string $problemaReportado;
    private ?string $diagnostico;
    private ?string $solucionAplicada;
    private string $estado;
    private ?float $costoEstimado;
    private ?float $costoFinal;
    private bool $autorizado;
    private ?DateTimeImmutable $fechaAutorizacion;
    private ?DateTimeImmutable $fechaEntrega;
    private ?string $observaciones;
    private DateTimeImmutable $createdAt;
    private DateTimeImmutable $updatedAt;

    public function __construct(
        string $id,
        string $codigoSeguimiento,
        string $clienteId,
        string $equipoId,
        ?string $tecnicoId,
        string $problemaReportado,
        ?string $diagnostico,
        ?string $solucionAplicada,
        string $estado,
        ?float $costoEstimado,
        ?float $costoFinal,
        bool $autorizado,
        ?DateTimeImmutable $fechaAutorizacion,
        ?DateTimeImmutable $fechaEntrega,
        ?string $observaciones,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt
    ) {
        $this->id = $id;
        $this->codigoSeguimiento = $codigoSeguimiento;
        $this->clienteId = $clienteId;
        $this->equipoId = $equipoId;
        $this->tecnicoId = $tecnicoId;
        $this->problemaReportado = $problemaReportado;
        $this->diagnostico = $diagnostico;
        $this->solucionAplicada = $solucionAplicada;
        $this->estado = $estado;
        $this->costoEstimado = $costoEstimado;
        $this->costoFinal = $costoFinal;
        $this->autorizado = $autorizado;
        $this->fechaAutorizacion = $fechaAutorizacion;
        $this->fechaEntrega = $fechaEntrega;
        $this->observaciones = $observaciones;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getCodigoSeguimiento(): string
    {
        return $this->codigoSeguimiento;
    }

    public function getClienteId(): string
    {
        return $this->clienteId;
    }

    public function getEquipoId(): string
    {
        return $this->equipoId;
    }

    public function getTecnicoId(): ?string
    {
        return $this->tecnicoId;
    }

    public function getProblemaReportado(): string
    {
        return $this->problemaReportado;
    }

    public function getDiagnostico(): ?string
    {
        return $this->diagnostico;
    }

    public function getSolucionAplicada(): ?string
    {
        return $this->solucionAplicada;
    }

    public function getEstado(): string
    {
        return $this->estado;
    }

    public function getCostoEstimado(): ?float
    {
        return $this->costoEstimado;
    }

    public function getCostoFinal(): ?float
    {
        return $this->costoFinal;
    }

    public function isAutorizado(): bool
    {
        return $this->autorizado;
    }

    public function getFechaAutorizacion(): ?DateTimeImmutable
    {
        return $this->fechaAutorizacion;
    }

    public function getFechaEntrega(): ?DateTimeImmutable
    {
        return $this->fechaEntrega;
    }

    public function getObservaciones(): ?string
    {
        return $this->observaciones;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public static function getEstados(): array
    {
        return [
            self::ESTADO_RECIBIDO,
            self::ESTADO_EN_DIAGNOSTICO,
            self::ESTADO_PENDIENTE_AUTORIZACION,
            self::ESTADO_AUTORIZADO,
            self::ESTADO_EN_REPARACION,
            self::ESTADO_REPARADO,
            self::ESTADO_ENTREGADO,
            self::ESTADO_CANCELADO,
        ];
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'codigoSeguimiento' => $this->codigoSeguimiento,
            'clienteId' => $this->clienteId,
            'equipoId' => $this->equipoId,
            'tecnicoId' => $this->tecnicoId,
            'problemaReportado' => $this->problemaReportado,
            'diagnostico' => $this->diagnostico,
            'solucionAplicada' => $this->solucionAplicada,
            'estado' => $this->estado,
            'costoEstimado' => $this->costoEstimado,
            'costoFinal' => $this->costoFinal,
            'autorizado' => $this->autorizado,
            'fechaAutorizacion' => $this->fechaAutorizacion?->format('Y-m-d H:i:s'),
            'fechaEntrega' => $this->fechaEntrega?->format('Y-m-d H:i:s'),
            'observaciones' => $this->observaciones,
            'createdAt' => $this->createdAt->format('Y-m-d H:i:s'),
            'updatedAt' => $this->updatedAt->format('Y-m-d H:i:s'),
        ];
    }
}
