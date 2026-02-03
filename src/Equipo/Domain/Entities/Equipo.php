<?php

declare(strict_types=1);

namespace Src\Equipo\Domain\Entities;

use DateTimeImmutable;

class Equipo
{
    private string $id;
    private string $clienteId;
    private string $marca;
    private string $modelo;
    private string $imei;
    private string $color;
    private ?string $observaciones;
    private DateTimeImmutable $createdAt;
    private DateTimeImmutable $updatedAt;

    public function __construct(
        string $id,
        string $clienteId,
        string $marca,
        string $modelo,
        string $imei,
        string $color,
        ?string $observaciones,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt
    ) {
        $this->id = $id;
        $this->clienteId = $clienteId;
        $this->marca = $marca;
        $this->modelo = $modelo;
        $this->imei = $imei;
        $this->color = $color;
        $this->observaciones = $observaciones;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getClienteId(): string
    {
        return $this->clienteId;
    }

    public function getMarca(): string
    {
        return $this->marca;
    }

    public function getModelo(): string
    {
        return $this->modelo;
    }

    public function getImei(): string
    {
        return $this->imei;
    }

    public function getColor(): string
    {
        return $this->color;
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

    public function getDescripcionCompleta(): string
    {
        return "{$this->marca} {$this->modelo} - {$this->color}";
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'clienteId' => $this->clienteId,
            'marca' => $this->marca,
            'modelo' => $this->modelo,
            'imei' => $this->imei,
            'color' => $this->color,
            'observaciones' => $this->observaciones,
            'createdAt' => $this->createdAt->format('Y-m-d H:i:s'),
            'updatedAt' => $this->updatedAt->format('Y-m-d H:i:s'),
        ];
    }
}
