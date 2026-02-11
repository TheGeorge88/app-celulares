<?php

namespace Src\Cliente\Infrastructure\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateClienteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $data = [];

        if ($this->has('userId')) {
            $data['user_id'] = $this->userId;
        }

        if ($this->has('tipoDocumento')) {
            $data['tipo_documento'] = $this->tipoDocumento;
        }

        if ($this->has('numeroDocumento')) {
            $data['numero_documento'] = $this->numeroDocumento;
        }

        if ($this->has('razonSocial')) {
            $data['razon_social'] = $this->razonSocial;
        }

        $this->merge($data);
    }

    public function rules(): array
    {
        $clienteId = $this->route('id') ?? $this->route('cliente');

        return [
            'user_id' => 'sometimes|uuid|exists:users,id|unique:clientes,user_id,' . $clienteId . ',id',
            'tipo_documento' => 'sometimes|string|in:DNI,RUC,CE,PASAPORTE',
            'numero_documento' => 'sometimes|string|unique:clientes,numero_documento,' . $clienteId . ',id',
            'razon_social' => 'sometimes|string|max:255',
            'direccion' => 'sometimes|string|max:255',
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
            'user_id.exists' => 'El usuario seleccionado no existe',
            'user_id.unique' => 'Este usuario ya tiene un cliente asignado',
            'tipo_documento.in' => 'El tipo de documento debe ser DNI, RUC, CE o PASAPORTE',
            'numero_documento.unique' => 'Este numero de documento ya esta registrado',
            'razon_social.required' => 'La razon social es obligatoria',
            'direccion.required' => 'La direccion es obligatoria',
        ];
    }
}
