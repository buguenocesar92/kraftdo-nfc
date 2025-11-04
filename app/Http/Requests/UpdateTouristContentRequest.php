<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTouristContentRequest extends FormRequest
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
            'location_name' => 'sometimes|nullable|string|max:255',
            'place_type' => 'sometimes|nullable|string|in:monumento,naturaleza,patrimonio,plaza,museo,iglesia,mirador,arquitectura,arqueologico,recreativo',
            'location_address' => 'sometimes|nullable|string|max:500',
            'history' => 'sometimes|nullable|string|max:5000',
            'latitude' => 'sometimes|nullable|numeric|between:-90,90',
            'longitude' => 'sometimes|nullable|numeric|between:-180,180',
            'practical_info' => 'sometimes|nullable|array',
            'gallery_images' => 'sometimes|nullable|array',
            'contact_phone' => 'sometimes|nullable|string|max:20',
            'contact_email' => 'sometimes|nullable|email|max:255',
            'website_url' => 'sometimes|nullable|url|max:500',
            'opening_hours' => 'sometimes|nullable|array',
            'pricing_info' => 'sometimes|nullable|array',
            'accessibility_info' => 'sometimes|nullable|array',
            'services' => 'sometimes|nullable|array',
            'attractions' => 'sometimes|nullable|array',
            'best_time_to_visit' => 'sometimes|nullable|string|max:1000',
            'languages_spoken' => 'sometimes|nullable|array',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'location_name.max' => 'El nombre de la ubicación no puede tener más de 255 caracteres',
            'place_type.in' => 'El tipo de lugar debe ser uno de los tipos válidos',
            'location_address.max' => 'La dirección no puede tener más de 500 caracteres',
            'history.max' => 'La historia no puede tener más de 5000 caracteres',
            'latitude.between' => 'La latitud debe estar entre -90 y 90',
            'longitude.between' => 'La longitud debe estar entre -180 y 180',
            'contact_phone.max' => 'El teléfono no puede tener más de 20 caracteres',
            'contact_email.email' => 'El email debe ser una dirección válida',
            'website_url.url' => 'La URL del sitio web debe ser válida',
            'website_url.max' => 'La URL del sitio web no puede tener más de 500 caracteres',
            'best_time_to_visit.max' => 'La mejor época para visitar no puede tener más de 1000 caracteres',
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