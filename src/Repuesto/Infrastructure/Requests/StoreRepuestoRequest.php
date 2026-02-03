<?php

declare(strict_types=1);

namespace Src\Repuesto\Infrastructure\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRepuestoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'codigo' => $this->codigo,
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'marca' => $this->marca,
            'modelo' => $this->modelo,
            'stock' => $this->stock ?? 0,
            'stock_minimo' => $this->stockMinimo ?? $this->stock_minimo ?? 5,
            'precio_compra' => $this->precioCompra ?? $this->precio_compra,
            'precio_venta' => $this->precioVenta ?? $this->precio_venta,
            'activo' => $this->activo ?? true,
        ]);
    }

    public function rules(): array
    {
        return [
            'codigo' => 'required|string|max:50|unique:repuestos,codigo',
            'nombre' => 'required|string|max:150',
            'descripcion' => 'nullable|string',
            'marca' => 'required|string|max:100',
            'modelo' => 'required|string|max:100',
            'stock' => 'sometimes|integer|min:0',
            'stock_minimo' => 'sometimes|integer|min:0',
            'precio_compra' => 'required|numeric|min:0',
            'precio_venta' => 'required|numeric|min:0',
            'activo' => 'sometimes|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'codigo.required' => 'El código es obligatorio',
            'codigo.unique' => 'El código ya está registrado',
            'nombre.required' => 'El nombre es obligatorio',
            'marca.required' => 'La marca es obligatoria',
            'modelo.required' => 'El modelo es obligatorio',
            'precio_compra.required' => 'El precio de compra es obligatorio',
            'precio_compra.numeric' => 'El precio de compra debe ser numérico',
            'precio_venta.required' => 'El precio de venta es obligatorio',
            'precio_venta.numeric' => 'El precio de venta debe ser numérico',
        ];
    }
}
