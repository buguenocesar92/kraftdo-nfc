<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
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
            'name' => 'sometimes|required|string|max:255',
            'price' => 'nullable|numeric|min:0|max:999999.99',
            'currency' => 'nullable|string|in:USD,EUR,GBP,CLP,MXN,ARS',
            'sku' => 'nullable|string|max:100',
            'stock' => 'nullable|integer|min:0',
            'in_stock' => 'nullable|boolean',
            'brand' => 'nullable|string|max:255',
            'specifications' => 'nullable|string|max:2000',
            'purchase_url' => 'nullable|url|max:500',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'El nombre del producto es obligatorio',
            'name.max' => 'El nombre del producto no puede tener más de 255 caracteres',
            'price.numeric' => 'El precio debe ser un número válido',
            'price.min' => 'El precio debe ser mayor o igual a 0',
            'price.max' => 'El precio no puede ser mayor a 999,999.99',
            'currency.in' => 'La moneda debe ser una de: USD, EUR, GBP, CLP, MXN, ARS',
            'stock.integer' => 'El stock debe ser un número entero',
            'stock.min' => 'El stock debe ser mayor o igual a 0',
            'specifications.max' => 'Las especificaciones no pueden tener más de 2000 caracteres',
            'purchase_url.url' => 'La URL de compra debe ser una URL válida',
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