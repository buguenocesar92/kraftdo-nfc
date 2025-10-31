<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGiftContentRequest extends FormRequest
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
            'message' => 'sometimes|nullable|string|max:2000',
            'recipient_name' => 'sometimes|nullable|string|max:255',
            'sender_name' => 'sometimes|nullable|string|max:255',
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

    /**
     * Get only the fields that were provided in the request
     */
    public function getUpdateData(): array
    {
        $data = [];
        
        if ($this->has('message')) {
            $data['message'] = $this->input('message');
        }
        
        if ($this->has('recipient_name')) {
            $data['recipient_name'] = $this->input('recipient_name');
        }
        
        if ($this->has('sender_name')) {
            $data['sender_name'] = $this->input('sender_name');
        }

        return $data;
    }
}