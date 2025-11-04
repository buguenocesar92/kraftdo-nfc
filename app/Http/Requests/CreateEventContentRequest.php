<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateEventContentRequest extends FormRequest
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
            'event_location' => 'nullable|string|max:500',
            'event_start_date' => 'nullable|date|after:now',
            'event_end_date' => 'nullable|date|after:event_start_date',
            'event_organizer' => 'nullable|string|max:255',
            'ticket_price' => 'nullable|numeric|min:0|max:999999.99',
            'ticket_currency' => 'nullable|string|in:USD,EUR,GBP,CLP,MXN,ARS',
            'registration_url' => 'nullable|url|max:500',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'event_location.max' => 'La ubicación del evento no puede tener más de 500 caracteres',
            'event_start_date.after' => 'La fecha de inicio debe ser posterior a la fecha actual',
            'event_end_date.after' => 'La fecha de fin debe ser posterior a la fecha de inicio',
            'event_organizer.max' => 'El nombre del organizador no puede tener más de 255 caracteres',
            'ticket_price.numeric' => 'El precio del ticket debe ser un número',
            'ticket_price.min' => 'El precio del ticket debe ser mayor o igual a 0',
            'ticket_price.max' => 'El precio del ticket no puede ser mayor a 999,999.99',
            'ticket_currency.in' => 'La moneda debe ser una de: USD, EUR, GBP, CLP, MXN, ARS',
            'registration_url.url' => 'La URL de registro debe ser una URL válida',
            'registration_url.max' => 'La URL de registro no puede tener más de 500 caracteres',
        ];
    }
}