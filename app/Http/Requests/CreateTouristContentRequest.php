<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateTouristContentRequest extends FormRequest
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
            'location_name' => 'nullable|string|max:255',
            'place_type' => 'nullable|string|in:monumento,naturaleza,patrimonio,plaza,museo,iglesia,mirador,arquitectura,arqueologico,recreativo',
            'location_address' => 'nullable|string|max:500',
            'history' => 'nullable|string|max:5000',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'practical_info' => 'nullable|array',
            'gallery_images' => 'nullable|array',
            'contact_phone' => 'nullable|string|max:20',
            'contact_email' => 'nullable|email|max:255',
            'website_url' => 'nullable|url|max:500',
            'opening_hours' => 'nullable|array',
            'pricing_info' => 'nullable|array',
            'accessibility_info' => 'nullable|array',
            'services' => 'nullable|array',
            'attractions' => 'nullable|array',
            'best_time_to_visit' => 'nullable|string|max:1000',
            'languages_spoken' => 'nullable|array',
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
}