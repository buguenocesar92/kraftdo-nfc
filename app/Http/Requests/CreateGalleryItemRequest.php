<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateGalleryItemRequest extends FormRequest
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
            'image_path' => 'required|string|max:500',
            'image_url' => 'nullable|url|max:500',
            'alt_text' => 'nullable|string|max:255',
            'caption' => 'nullable|string|max:500',
            'sort_order' => 'nullable|integer|min:0',
            'type' => 'nullable|string|in:gallery,cover,banner',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'image_path.required' => 'La ruta de la imagen es obligatoria',
            'image_path.max' => 'La ruta de la imagen no puede tener más de 500 caracteres',
            'image_url.url' => 'La URL de la imagen debe ser una URL válida',
            'alt_text.max' => 'El texto alternativo no puede tener más de 255 caracteres',
            'caption.max' => 'El caption no puede tener más de 500 caracteres',
            'sort_order.integer' => 'El orden debe ser un número entero',
            'sort_order.min' => 'El orden debe ser mayor o igual a 0',
            'type.in' => 'El tipo debe ser: gallery, cover o banner',
        ];
    }
}