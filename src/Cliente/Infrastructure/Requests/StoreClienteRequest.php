<?php

namespace Src\Cliente\Infrastructure\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreClienteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'user_id' => $this->userId,
            'tipo_documento' => $this->tipoDocumento,
            'numero_documento' => $this->numeroDocumento,
            'razon_social' => $this->razonSocial,
        ]);
    }

    public function rules(): array
    {
        return [
            'user_id' => 'required|uuid|exists:users,id|unique:clientes,user_id',
            'tipo_documento' => 'required|string|in:DNI,RUC,CE,PASAPORTE',
            'numero_documento' => 'required|string|unique:clientes,numero_documento',
            'razon_social' => 'required|string|max:255',
            'direccion' => 'required|string|max:255',
        ];
    }

    public function attributes(): array
    {
        return [
            'user_id' => 'usuario',
            'tipo_documento' => 'tipo de documento',
            'numero_documento' => 'numero de documento',
            'razon_social' => 'razon social',
            'direccion' => 'direccion',
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'El usuario es obligatorio',
            'user_id.exists' => 'El usuario seleccionado no existe',
            'user_id.unique' => 'Este usuario ya tiene un cliente asignado',
            'tipo_documento.required' => 'El tipo de documento es obligatorio',
            'tipo_documento.in' => 'El tipo de documento debe ser DNI, RUC, CE o PASAPORTE',
            'numero_documento.required' => 'El numero de documento es obligatorio',
            'numero_documento.unique' => 'Este numero de documento ya esta registrado',
            'razon_social.required' => 'La razon social es obligatoria',
            'direccion.required' => 'La direccion es obligatoria',
        ];
    }
}
