<?php

namespace Src\Cliente\Domain\Entities;
use DateTimeImmutable;

class Cliente
{
    private string $id;
    private string $userId;
    private string $tipoDocumento;
    private string $numeroDocumento;
    private string $razonSocial;
    private string $direccion;
    private DateTimeImmutable $createdAt;
    private DateTimeImmutable $updatedAt;

    public function __construct(
        string $id,
        string $userId,
        string $tipoDocumento,
        string $numeroDocumento,
        string $razonSocial,
        string $direccion,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt
    ) {
        $this->id = $id;
        $this->userId = $userId;
        $this->tipoDocumento = $tipoDocumento;
        $this->numeroDocumento = $numeroDocumento;
        $this->razonSocial = $razonSocial;
        $this->direccion = $direccion;
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

    public function getTipoDocumento(): string
    {
        return $this->tipoDocumento;
    }

    public function getNumeroDocumento(): string
    {
        return $this->numeroDocumento;
    }

    public function getRazonSocial(): string
    {
        return $this->razonSocial;
    }

    public function getDireccion(): string
    {
        return $this->direccion;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function updateUserId(string $userId): void
    {
        $this->userId = $userId;
    }

    public function updateTipoDocumento(string $tipoDocumento): void
    {
        $this->tipoDocumento = $tipoDocumento;
    }

    public function updateNumeroDocumento(string $numeroDocumento): void
    {
        $this->numeroDocumento = $numeroDocumento;
    }

    public function updateRazonSocial(string $razonSocial): void
    {
        $this->razonSocial = $razonSocial;
    }

    public function updateDireccion(string $direccion): void
    {
        $this->direccion = $direccion;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'userId' => $this->userId,
            'tipoDocumento' => $this->tipoDocumento,
            'numeroDocumento' => $this->numeroDocumento,
            'razonSocial' => $this->razonSocial,
            'direccion' => $this->direccion,
            'createdAt' => $this->createdAt->format('Y-m-d H:i:s'),
            'updatedAt' => $this->updatedAt->format('Y-m-d H:i:s'),
        ];
    }
}
