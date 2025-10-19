<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateBusinessContentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Handle authorization in middleware/policies
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'address' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:500',
            'logo' => 'nullable|string|max:500',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'operating_hours' => 'nullable|array',
            'operating_hours.*.day' => 'required_with:operating_hours|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'operating_hours.*.hours' => 'required_with:operating_hours|string|max:50',
            'catalog_enabled' => 'boolean',
            'menu_images' => 'nullable|array',
            'menu_images.*.url' => 'required_with:menu_images|url',
            'menu_images.*.title' => 'nullable|string|max:255',
            'social_links' => 'nullable|array',
            'social_links.*.platform' => 'required_with:social_links|string|max:50',
            'social_links.*.url' => 'required_with:social_links|url',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'El nombre del negocio es obligatorio',
            'name.max' => 'El nombre no puede tener más de 255 caracteres',
            'email.email' => 'El formato del email no es válido',
            'website.url' => 'El sitio web debe ser una URL válida',
            'latitude.between' => 'La latitud debe estar entre -90 y 90 grados',
            'longitude.between' => 'La longitud debe estar entre -180 y 180 grados',
            'operating_hours.*.day.in' => 'El día debe ser uno de: monday, tuesday, wednesday, thursday, friday, saturday, sunday',
            'menu_images.*.url.url' => 'Cada imagen del menú debe tener una URL válida',
            'social_links.*.url.url' => 'Cada enlace social debe tener una URL válida',
        ];
    }
}
