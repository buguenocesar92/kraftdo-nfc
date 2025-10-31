<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateGiftContentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization handled by middleware
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'message' => 'nullable|string|max:2000',
            'recipient_name' => 'nullable|string|max:255',
            'sender_name' => 'nullable|string|max:255',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'message.max' => 'El mensaje no puede exceder los 2000 caracteres.',
            'recipient_name.max' => 'El nombre del destinatario no puede exceder los 255 caracteres.',
            'sender_name.max' => 'El nombre del remitente no puede exceder los 255 caracteres.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'message' => 'mensaje',
            'recipient_name' => 'nombre del destinatario',
            'sender_name' => 'nombre del remitente',
        ];
    }
}