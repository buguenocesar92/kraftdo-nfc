<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateSocialLinkRequest extends FormRequest
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
            'platform' => 'required|string|max:50',
            'url' => 'required|url|max:500',
            'username' => 'nullable|string|max:100',
            'sort_order' => 'nullable|integer|min:0',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'platform.required' => 'La plataforma es obligatoria',
            'platform.max' => 'La plataforma no puede tener más de 50 caracteres',
            'url.required' => 'La URL es obligatoria',
            'url.url' => 'La URL debe tener un formato válido',
            'url.max' => 'La URL no puede tener más de 500 caracteres',
            'username.max' => 'El nombre de usuario no puede tener más de 100 caracteres',
            'sort_order.integer' => 'El orden debe ser un número entero',
            'sort_order.min' => 'El orden debe ser mayor o igual a 0',
        ];
    }
}