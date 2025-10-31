<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBusinessContentRequest extends FormRequest
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
            'business_name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'business_type' => 'nullable|string|max:100',
            'logo_url' => 'nullable|url|max:500',
            'contact_phone' => 'nullable|string|max:20',
            'contact_email' => 'nullable|email|max:255',
            'contact_website' => 'nullable|url|max:500',
            'address' => 'nullable|string|max:500',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'google_maps_url' => 'nullable|url|max:500',
            'google_reviews_url' => 'nullable|url|max:500',
            'google_place_id' => 'nullable|string|max:255',
            'instagram_url' => 'nullable|url|max:500',
            'facebook_url' => 'nullable|url|max:500',
            'whatsapp_number' => 'nullable|string|max:20',
            'operating_hours' => 'nullable|array',
            'services' => 'nullable|array',
            'catalog_enabled' => 'nullable|boolean',
            'color_palette' => 'nullable|array',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'business_name.required' => 'El nombre del negocio es obligatorio',
            'business_name.max' => 'El nombre del negocio no puede tener más de 255 caracteres',
            'contact_email.email' => 'El email debe tener un formato válido',
            'contact_website.url' => 'El sitio web debe ser una URL válida',
            'description.max' => 'La descripción no puede tener más de 1000 caracteres',
            'latitude.between' => 'La latitud debe estar entre -90 y 90',
            'longitude.between' => 'La longitud debe estar entre -180 y 180',
        ];
    }

    /**
     * Get only the fields that should be updated
     */
    public function getUpdateData(): array
    {
        $updateData = [];
        
        foreach ($this->validated() as $key => $value) {
            if ($this->has($key)) {
                $updateData[$key] = $value;
            }
        }
        
        return $updateData;
    }
}