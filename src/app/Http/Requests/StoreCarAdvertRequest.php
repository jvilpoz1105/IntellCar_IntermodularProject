<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCarAdvertRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // La autorización se maneja en el controlador
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'ad_title' => 'required|string|max:165',
            'ad_type' => 'required|in:new,km0,used,renting,leasing,supcription',
            'ad_details' => 'nullable|string',
            'price' => 'required|numeric|min:0|max:9999999999.99',
            'kilometers' => 'nullable|integer|min:0',
            'car_color' => 'required|in:blanco,negro,gris,plata,rojo,azul,verde,amarillo,naranja,otro',
            'year_manufacture' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'region' => 'required|string|max:100',
            'city' => 'required|string|max:100',
            'model_id' => 'required|exists:car_model,model_id',
            'engine_id' => 'required|exists:car_engine,engine_id',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'ad_title.required' => 'El título del anuncio es obligatorio.',
            'ad_title.max' => 'El título no puede superar los 165 caracteres.',
            'ad_type.required' => 'El tipo de anuncio es obligatorio.',
            'ad_type.in' => 'El tipo de anuncio no es válido.',
            'price.required' => 'El precio es obligatorio.',
            'price.numeric' => 'El precio debe ser un número.',
            'price.min' => 'El precio no puede ser negativo.',
            'car_color.required' => 'El color del coche es obligatorio.',
            'region.required' => 'La región es obligatoria.',
            'city.required' => 'La ciudad es obligatoria.',
            'model_id.required' => 'El modelo del coche es obligatorio.',
            'model_id.exists' => 'El modelo seleccionado no existe.',
            'engine_id.required' => 'El motor es obligatorio.',
            'engine_id.exists' => 'El motor seleccionado no existe.',
        ];
    }
}
