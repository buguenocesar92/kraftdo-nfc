<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBusStopContentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'stop_id' => 'sometimes|nullable|string|max:50',
            'name' => 'sometimes|nullable|string|max:255',
            'address' => 'sometimes|nullable|string|max:500',
            'latitude' => 'sometimes|nullable|numeric|between:-90,90',
            'longitude' => 'sometimes|nullable|numeric|between:-180,180',
            'municipality_name' => 'sometimes|nullable|string|max:255',
            'municipality_logo_url' => 'sometimes|nullable|url|max:500',
            'municipality_description' => 'sometimes|nullable|string|max:1000',
            'municipality_website' => 'sometimes|nullable|url|max:500',
            'is_active' => 'sometimes|nullable|boolean',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'stop_id.max' => 'El ID de la parada no puede tener más de 50 caracteres',
            'name.max' => 'El nombre de la parada no puede tener más de 255 caracteres',
            'address.max' => 'La dirección no puede tener más de 500 caracteres',
            'latitude.between' => 'La latitud debe estar entre -90 y 90',
            'longitude.between' => 'La longitud debe estar entre -180 y 180',
            'municipality_name.max' => 'El nombre del municipio no puede tener más de 255 caracteres',
            'municipality_logo_url.url' => 'La URL del logo del municipio debe ser válida',
            'municipality_logo_url.max' => 'La URL del logo no puede tener más de 500 caracteres',
            'municipality_description.max' => 'La descripción del municipio no puede tener más de 1000 caracteres',
            'municipality_website.url' => 'La URL del sitio web del municipio debe ser válida',
            'municipality_website.max' => 'La URL del sitio web no puede tener más de 500 caracteres',
            'is_active.boolean' => 'El campo activo debe ser verdadero o falso',
        ];
    }

    /**
     * Get the filtered update data (only present fields)
     */
    public function getUpdateData(): array
    {
        return array_filter($this->validated(), function ($value) {
            return $value !== null;
        });
    }
}