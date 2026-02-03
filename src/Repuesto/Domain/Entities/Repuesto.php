<?php

declare(strict_types=1);

namespace Src\Repuesto\Domain\Entities;

use DateTimeImmutable;

class Repuesto
{
    private string $id;
    private string $codigo;
    private string $nombre;
    private string $descripcion;
    private string $marca;
    private string $modelo;
    private int $stock;
    private int $stockMinimo;
    private float $precioCompra;
    private float $precioVenta;
    private bool $activo;
    private DateTimeImmutable $createdAt;
    private DateTimeImmutable $updatedAt;

    public function __construct(
        string $id,
        string $codigo,
        string $nombre,
        string $descripcion,
        string $marca,
        string $modelo,
        int $stock,
        int $stockMinimo,
        float $precioCompra,
        float $precioVenta,
        bool $activo,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt
    ) {
        $this->id = $id;
        $this->codigo = $codigo;
        $this->nombre = $nombre;
        $this->descripcion = $descripcion;
        $this->marca = $marca;
        $this->modelo = $modelo;
        $this->stock = $stock;
        $this->stockMinimo = $stockMinimo;
        $this->precioCompra = $precioCompra;
        $this->precioVenta = $precioVenta;
        $this->activo = $activo;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getCodigo(): string
    {
        return $this->codigo;
    }

    public function getNombre(): string
    {
        return $this->nombre;
    }

    public function getDescripcion(): string
    {
        return $this->descripcion;
    }

    public function getMarca(): string
    {
        return $this->marca;
    }

    public function getModelo(): string
    {
        return $this->modelo;
    }

    public function getStock(): int
    {
        return $this->stock;
    }

    public function getStockMinimo(): int
    {
        return $this->stockMinimo;
    }

    public function getPrecioCompra(): float
    {
        return $this->precioCompra;
    }

    public function getPrecioVenta(): float
    {
        return $this->precioVenta;
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

    public function tieneStockBajo(): bool
    {
        return $this->stock <= $this->stockMinimo;
    }

    public function hayDisponibilidad(int $cantidad): bool
    {
        return $this->stock >= $cantidad;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'codigo' => $this->codigo,
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'marca' => $this->marca,
            'modelo' => $this->modelo,
            'stock' => $this->stock,
            'stockMinimo' => $this->stockMinimo,
            'precioCompra' => $this->precioCompra,
            'precioVenta' => $this->precioVenta,
            'activo' => $this->activo,
            'createdAt' => $this->createdAt->format('Y-m-d H:i:s'),
            'updatedAt' => $this->updatedAt->format('Y-m-d H:i:s'),
        ];
    }
}
