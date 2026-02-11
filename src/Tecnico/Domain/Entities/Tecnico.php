<?php

declare(strict_types=1);

namespace Src\Tecnico\Domain\Entities;

use DateTimeImmutable;

class Tecnico
{
    private string $id;
    private string $userId;
    private string $especialidad;
    private ?string $certificacion;
    private ?DateTimeImmutable $fechaContratacion;
    private bool $activo;
    private DateTimeImmutable $createdAt;
    private DateTimeImmutable $updatedAt;

    public function __construct(
        string $id,
        string $userId,
        string $especialidad,
        ?string $certificacion,
        ?DateTimeImmutable $fechaContratacion,
        bool $activo,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt
    ) {
        $this->id = $id;
        $this->userId = $userId;
        $this->especialidad = $especialidad;
        $this->certificacion = $certificacion;
        $this->fechaContratacion = $fechaContratacion;
        $this->activo = $activo;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function getEspecialidad(): string
    {
        return $this->especialidad;
    }

    public function getCertificacion(): ?string
    {
        return $this->certificacion;
    }

    public function getFechaContratacion(): ?DateTimeImmutable
    {
        return $this->fechaContratacion;
    }

    public function isActivo(): bool
    {
        return $this->activo;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'userId' => $this->userId,
            'especialidad' => $this->especialidad,
            'certificacion' => $this->certificacion,
            'fechaContratacion' => $this->fechaContratacion?->format('Y-m-d'),
            'activo' => $this->activo,
            'createdAt' => $this->createdAt->format('Y-m-d H:i:s'),
            'updatedAt' => $this->updatedAt->format('Y-m-d H:i:s'),
        ];
    }
}
