<?php

declare(strict_types=1);

namespace Src\Repuesto\Infrastructure\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRepuestoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $data = [];

        if ($this->has('codigo')) $data['codigo'] = $this->codigo;
        if ($this->has('nombre')) $data['nombre'] = $this->nombre;
        if ($this->has('descripcion')) $data['descripcion'] = $this->descripcion;
        if ($this->has('marca')) $data['marca'] = $this->marca;
        if ($this->has('modelo')) $data['modelo'] = $this->modelo;
        if ($this->has('stock')) $data['stock'] = $this->stock;
        if ($this->has('stockMinimo') || $this->has('stock_minimo')) {
            $data['stock_minimo'] = $this->stockMinimo ?? $this->stock_minimo;
        }
        if ($this->has('precioCompra') || $this->has('precio_compra')) {
            $data['precio_compra'] = $this->precioCompra ?? $this->precio_compra;
        }
        if ($this->has('precioVenta') || $this->has('precio_venta')) {
            $data['precio_venta'] = $this->precioVenta ?? $this->precio_venta;
        }
        if ($this->has('activo')) $data['activo'] = $this->activo;

        $this->merge($data);
    }

    public function rules(): array
    {
        $repuestoId = $this->route('id') ?? $this->route('repuesto');

        return [
            'codigo' => 'sometimes|string|max:50|unique:repuestos,codigo,' . $repuestoId,
            'nombre' => 'sometimes|string|max:150',
            'descripcion' => 'nullable|string',
            'marca' => 'sometimes|string|max:100',
            'modelo' => 'sometimes|string|max:100',
            'stock' => 'sometimes|integer|min:0',
            'stock_minimo' => 'sometimes|integer|min:0',
            'precio_compra' => 'sometimes|numeric|min:0',
            'precio_venta' => 'sometimes|numeric|min:0',
            'activo' => 'sometimes|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'codigo.unique' => 'El código ya está registrado',
            'precio_compra.numeric' => 'El precio de compra debe ser numérico',
            'precio_venta.numeric' => 'El precio de venta debe ser numérico',
        ];
    }
}
