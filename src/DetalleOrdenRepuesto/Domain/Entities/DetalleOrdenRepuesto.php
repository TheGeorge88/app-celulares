<?php

declare(strict_types=1);

namespace Src\DetalleOrdenRepuesto\Domain\Entities;

use DateTimeImmutable;

class DetalleOrdenRepuesto
{
    private string $id;
    private string $ordenReparacionId;
    private string $repuestoId;
    private int $cantidad;
    private float $precioUnitario;
    private float $subtotal;
    private DateTimeImmutable $createdAt;
    private DateTimeImmutable $updatedAt;

    public function __construct(
        string $id,
        string $ordenReparacionId,
        string $repuestoId,
        int $cantidad,
        float $precioUnitario,
        float $subtotal,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt
    ) {
        $this->id = $id;
        $this->ordenReparacionId = $ordenReparacionId;
        $this->repuestoId = $repuestoId;
        $this->cantidad = $cantidad;
        $this->precioUnitario = $precioUnitario;
        $this->subtotal = $subtotal;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getOrdenReparacionId(): string
    {
        return $this->ordenReparacionId;
    }

    public function getRepuestoId(): string
    {
        return $this->repuestoId;
    }

    public function getCantidad(): int
    {
        return $this->cantidad;
    }

    public function getPrecioUnitario(): float
    {
        return $this->precioUnitario;
    }

    public function getSubtotal(): float
    {
        return $this->subtotal;
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
            'ordenReparacionId' => $this->ordenReparacionId,
            'repuestoId' => $this->repuestoId,
            'cantidad' => $this->cantidad,
            'precioUnitario' => $this->precioUnitario,
            'subtotal' => $this->subtotal,
            'createdAt' => $this->createdAt->format('Y-m-d H:i:s'),
            'updatedAt' => $this->updatedAt->format('Y-m-d H:i:s'),
        ];
    }
}
