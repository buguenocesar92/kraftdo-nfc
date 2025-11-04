<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateBusStopContentRequest extends FormRequest
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
            'stop_id' => 'nullable|string|max:50',
            'name' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:500',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'municipality_name' => 'nullable|string|max:255',
            'municipality_logo_url' => 'nullable|url|max:500',
            'municipality_description' => 'nullable|string|max:1000',
            'municipality_website' => 'nullable|url|max:500',
            'is_active' => 'nullable|boolean',
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
}